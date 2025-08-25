async function copyText(text) {
    // 方法1: 尝试 Clipboard API
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.log('Clipboard API 失败，尝试其他方法');
        }
    }

    // 方法2: 使用 execCommand
    try {
        const textArea = document.createElement('input');
        textArea.value = text;
        textArea.style.position = 'absolute';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        // 选中文本
        textArea.select();
        textArea.setSelectionRange(0, 99999); // 移动端支持

        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        if (successful) {
            return true;
        }
    } catch (err) {
        console.error('execCommand 也失败了:', err);
    }

    // 方法3: 提示用户手动复制
    console.log('自动复制失败，请手动复制');
    return false;
}

(function () {
    'use strict';

    /**
     * 导航菜单激活器
     */
    class MenuActivator {
        constructor() {
            this.currentPath = location.pathname;
            this.menuContainer = document.getElementById("navbar-menu");
        }

        /**
         * 初始化激活菜单
         */
        init() {
            if (!this.currentPath || !this.menuContainer) {
                return;
            }

            const activeLink = this.findActiveLink();
            if (activeLink) {
                this.activateMenuHierarchy(activeLink);
            }
        }

        /**
         * 查找活跃的链接元素
         * @returns {Element|null}
         */
        findActiveLink() {
            const links = Array.from(this.menuContainer.querySelectorAll("a[href]"));

            // 优先寻找精确匹配
            let exactMatch = null;
            let partialMatch = null;

            for (const link of links) {
                const href = this.normalizeHref(link.getAttribute('href'));

                if (href === this.currentPath) {
                    exactMatch = link;
                    break;
                }

                // 部分匹配：当前路径以href开头且href不是根路径
                if (href !== '/' && this.currentPath.startsWith(href + '/')) {
                    if (!partialMatch || href.length > this.normalizeHref(partialMatch.getAttribute('href')).length) {
                        partialMatch = link;
                    }
                }
            }

            return exactMatch || partialMatch;
        }

        /**
         * 标准化href路径
         * @param {string} href
         * @returns {string}
         */
        normalizeHref(href) {
            if (!href) return '';

            // 提取绝对URL中的路径部分
            const match = href.match(/^https?:\/\/[^\/]+(.*)$/);
            const path = match ? match[1] || '/' : href;

            // 移除末尾斜杠（除非是根路径）
            return path === '/' ? path : path.replace(/\/$/, '');
        }

        /**
         * 激活菜单层次结构
         * @param {Element} activeLink
         */
        activateMenuHierarchy(activeLink) {
            activeLink.classList.add('active');
            // 激活链接所在的nav-item
            const navItem = activeLink.closest(".nav-item");
            navItem?.classList.add("active");
        }
    }

    /**
     * 初始化函数
     */
    function initActiveMenu() {
        const menuActivator = new MenuActivator();
        menuActivator.init();
    }

    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initActiveMenu);
    } else {
        initActiveMenu();
    }

    // 支持SPA路由变化
    window.addEventListener('popstate', initActiveMenu);
})();

class LazyLoad {
    constructor(element, options) {
        this.element = element;
        this.options = options;
        this.loaded = false;
        this.observer = null;
        this.init();
    }

    init() {
        if (this.observer) {
            this.observer.disconnect(); // 清理之前的 observer
        }
        let that = this;
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (that.loaded) {
                        return;
                    }
                    that.load(entry.target);
                }
            });
        }, {
            threshold: 0.1, // 当10%的元素可见时触发
            rootMargin: '0px' // 可以设置边距
        });
        this.observer.observe(this.element);
    }

    async load(el) {
        let renderable = el.getAttribute("data-renderable");
        let payload = el.getAttribute("data-payload");
        let response = await fetch('/merlion-api/lazy-render?renderable=' + renderable + '&payload=' + payload);
        el.innerHTML = await response.text();
        this.loaded = true;
        admin().init(el);
    }
}

class Merlion {
    static getInstance() {
        if (!Merlion.instance) {
            Merlion.instance = new Merlion()
        }
        return Merlion.instance
    }

    init(container) {
        if (!container) {
            container = document;
        }
        this.initCopyable(container);
        this.initActions(container);
        this.initBatchActions(container);
        this.initLazyLoad(container);
        this.initTable(container);
        return 'ok';
    }

    initCopyable(container) {
        container.querySelectorAll('[data-copyable]').forEach(function (element) {
            const value = element.getAttribute('data-copyable');
            if (!value) {
                return;
            }
            element.insertAdjacentHTML('beforeend', '<i role="button" class="ti ti-clipboard"></i>');
            element.querySelector('i.ti-clipboard').addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                let textToCopy = element.getAttribute('data-copyable');
                console.log(textToCopy);
                copyText(element.getAttribute('data-copyable'))
                    .then(() => {
                        element.querySelector('i').classList.add('ti-clipboard-check', 'text-success');
                        element.querySelector('i').classList.remove('ti-clipboard');
                        setTimeout(function () {
                            element.querySelector('i').classList.remove('ti-clipboard-check', 'text-success');
                            element.querySelector('i').classList.add('ti-clipboard');
                            console.log('成功:');
                        }, 2000);
                    })
                    .catch(err => {
                        console.error('复制失败:', err);
                    });
            })
        });
    }

    initActions(container) {
        let that = this;
        container.querySelectorAll("[data-action]").forEach(function (el) {
            el.addEventListener('click', async function (e) {
                e.stopPropagation();
                e.preventDefault();
                let button = e.currentTarget;
                const confirm_title = button.getAttribute('data-confirm');
                if (confirm_title) {
                    let result = await that.showConfirm(confirm_title);
                    if (!result.isConfirmed) {
                        return;
                    }
                }

                const action = button.getAttribute('data-action');
                const data = button.getAttribute('data-payload');
                const method = button.getAttribute('data-method') || 'post';

                fetch(action, {
                    method: method,
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSP-NONCE': document.querySelector('meta[name="csp-nonce"]').getAttribute('content'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: data,
                }).then(response => {
                    return response.json();
                }).then(data => {
                    that.handleFetchData(data);
                }).catch(error => {
                    console.error(error);
                }).finally(() => {
                    button.classList.remove('disabled');
                });
            });
        })
    }

    initBatchActions(container) {
        let that = this;
        container.querySelectorAll("[data-batch-action]").forEach(function (el) {
            const action = el.getAttribute('data-batch-action');
            if (!action) {
                return;
            }
            el.addEventListener('click', async function (e) {
                e.stopPropagation();
                e.preventDefault();
                let button = e.currentTarget;
                const table_id = button.getAttribute('data-table');
                const selected_ids = container.querySelector(`#${table_id}`).querySelectorAll('.row-select:checked');
                const ids = selected_ids ? Array.from(selected_ids).map(el => el.value) : null;
                if (ids.length === 0) {
                    return;
                }
                const confirm_title = button.getAttribute('data-confirm');
                if (confirm_title) {
                    const result = await that.showConfirm(confirm_title, 'total:' + ids.length);
                    if (!result.isConfirmed) {
                        return;
                    }
                }
                const action = button.getAttribute('data-batch-action');
                const data = button.getAttribute('data-payload') || {};
                const method = button.getAttribute('data-method') || 'post';

                data.ids = ids;
                button.classList.add('disabled');
                fetch(action, {
                    method: method,
                    headers: {
                        "Content-type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        'X-CSP-NONCE': document.querySelector('meta[name="csp-nonce"]').getAttribute('content'),
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(data)
                }).then(response => {
                    return response.json()
                }).then(data => {
                    that.handleFetchData(data);
                }).catch(error => {
                    console.error(error);
                }).finally(() => {
                    button.classList.remove('disabled');
                });
            });
        })
    }

    initLazyLoad(container) {
        container.querySelectorAll('[data-lazy]').forEach(function (element) {
            new LazyLoad(element, {});
        });
    }

    initTable(container) {
        container.querySelectorAll('.table-selectable').forEach(function (element) {
            element.querySelectorAll('.row-select-all').forEach(function (checkbox) {
                checkbox.addEventListener('change', function (e) {
                    element.querySelectorAll('.row-select').forEach(function (row_checkbox) {
                        row_checkbox.checked = e.target.checked;
                    });
                })
            });
        });
    }

    showToast(options) {
        Toastify({
            text: options.message,
            duration: (options.duration || 3000),
            position: (options.position || 'center'),
            gravity: (options.gravity || 'top'),
            stopOnFocus: true,
            style: {
                background: "unset",
            },
            className: "bg-" + (options.type || 'primary'),
        }).showToast();
    }

    handleFetchData(data, onsuccess, onerror) {
        if (data && data.action) {
            switch (data.action) {
                case 'refresh':
                case 'reload':
                    location.reload();
                    break;
                case 'rediret':
                    location.href = data.url;
                    break;
            }
            if (onsuccess) {
                onsuccess(data);
            }
        } else {
            if (onerror) {
                onerror(data);
            }
            Swal.fire({
                icon: "error",
                title: data.message,
            })
        }
    }

    async showConfirm(title, text) {
        return await Swal.fire({
            title: title,
            text: text,
            icon: "warning",
            showCancelButton: true,
        })
    }
}

window.admin = function () {
    return Merlion.getInstance();
}

document.addEventListener('DOMContentLoaded', function () {
    admin().init();
});

