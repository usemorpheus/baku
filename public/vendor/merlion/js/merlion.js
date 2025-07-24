class Merlion {
    cspNonce;

    booted = false;

    static make() {
        if (!window._merlion) {
            window._merlion = new Merlion();
            window._merlion.init();
            window._merlion.boot();
        }
        return window._merlion;
    }

    constructor(props) {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;
                    admin().loadRenderable(target);
                }
            });
        });
    }

    init() {
        this.initCstfToken();
        this.initCspNonce();
    }

    boot() {
        if (this.booted) {
            return;
        }
        this.initActionButton();
        this.booted = true;
    }

    initCstfToken() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            $.ajaxSetup({headers: {'X-CSRF-TOKEN': csrfToken}});
        }
    }

    initCspNonce() {
        const metaElement = document.querySelector('meta[name="csp-nonce"]');
        if (metaElement) {
            this.cspNonce = metaElement.getAttribute('content');
        } else {
            console.log('can not found csp nonce');
        }
    }

    initActionButton() {
        console.log('initactionbutton');
        $('[data-action]').on('click', async function (event) {
            let confirm_message = $(this).data('confirm');

            if (confirm_message) {
                if (!confirm(confirm_message)) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
            }

            let action = $(this).data('action');
            let method = $(this).data('method');
            let payload = $(this).data('payload') || {};

            if ($(this).data('table')) {
                payload.ids = $('#' + $(this).data('table') + ' .check:checked').map(function () {
                    return $(this).data('id');
                }).get();
            }

            $.ajax(action, {
                method: method, data: payload, success: function (response) {
                    admin().response(response);
                }, error: function (error) {
                    console.log(error);
                }
            });
        });
    }

    toast(toastData) {
        Toastify({
            newWindow: true,
            text: toastData.text,
            gravity: toastData.gravity,
            position: toastData.position,
            className: "bg-" + toastData.className,
            stopOnFocus: true,
            escapeMarkup: false,
            offset: {
                x: toastData.offset ? 50 : 0, // horizontal axis - can be a number or a string indicating unity. eg: '2em'
                y: toastData.offset ? 10 : 0, // vertical axis - can be a number or a string indicating unity. eg: '2em'
            },
            duration: toastData.duration,
            close: toastData.close === "close",
            style: toastData.style === "style" ? {
                background: "linear-gradient(to right, var(--vz-success), var(--vz-primary))"
            } : "",
        }).showToast();
    }

    params(url) {
        let params = {};
        try {
            // 创建 URL 对象
            const urlObj = new URL(url);

            // 获取 URLSearchParams 对象
            const searchParams = urlObj.searchParams;

            // 遍历所有参数并添加到对象中
            for (const [key, value] of searchParams) {
                params[key] = value;
            }

            return params;
        } catch (error) {
            console.error('Invalid URL:', error);
            return {};
        }
    }

    response(response) {
        // check if response is json

        if (typeof response === 'string') {
            response = JSON.parse(response);
        }

        switch (response.action) {
            case 'alert':
                alert(response.message);
                break;
            case 'redirect':
                window.location.href = response.url;
                break;
            case 'back':
                window.history.back();
                break;
            case 'reload':
            case 'refresh':
                window.location.reload();
                break;
            case 'dismiss':
            case 'close':
                $(`.modal`).modal('hide');
                break;
        }
    }

    errors(detail, container) {
        for (let key in detail) {
            $(`input[name=${key}]`).addClass('is-invalid');
        }
    }

    asyncRender(url, done, error) {
        $.ajax({
            type: 'get', url: url
        }).then(function (data) {
            done(data);
        }, function (a, b, c) {
            if (error) {
                if (error(a, b, c) === false) {
                    return false;
                }
            }
        })
    }

    selectTable(options) {
        return new SelectTable(options);
    }

    lazyForm(options) {
        return new LazyForm(options);
    }

    async copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(text);
                return true;
            } catch (err) {
                console.error(err);
            }
        }

        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';

        document.body.appendChild(textarea);
        textarea.select();

        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(textarea);

            if (successful) {
                console.log('文本已复制到剪贴板');
                return true;
            } else {
                console.error('复制失败');
                return false;
            }
        } catch (err) {
            document.body.removeChild(textarea);
            console.error('复制失败:', err);
            return false;
        }
    }

    post(url, data, callback, onerror) {
        fetch(url, {
            method: 'POST',
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                'X-CSP-NONCE': document.querySelector('meta[name="csp-nonce"]').getAttribute('content'),
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify(data),
        })
            .then(response => {
                if (response.ok) {
                    return response.json()
                }
                onerror(response);
            })
            .then(data => {
                callback(data);
            })
            .catch(error => {
                onerror(error);
            });
    }

    async executeScriptsSequentially(scripts) {
        for (const script of scripts) {
            await admin().executeScript(script);
        }
    }

    async loadContent(url, target, data) {
        console.log('loadContent', url);

        const _csp = document.querySelector('meta[name="csp-nonce"]').getAttribute('content');
        const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const response = await fetch(url, {
            method: 'POST',
            body: data,
            headers: {
                "X-CSP-NONCE": _csp,
                "X-CSRF-TOKEN": csrf_token,
                "X-Requested-With": "XMLHttpRequest"
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const htmlContent = await response.text();
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        // 提取并处理style标签
        const styles = tempDiv.querySelectorAll('style');
        styles.forEach(style => {
            const newStyle = document.createElement('style');
            newStyle.textContent = style.textContent;
            document.head.appendChild(newStyle);
        });

        // 提取并处理link标签（CSS链接）
        const links = tempDiv.querySelectorAll('link[rel="stylesheet"]');
        links.forEach(link => {
            const newLink = document.createElement('link');
            newLink.rel = 'stylesheet';
            newLink.href = link.href;
            document.head.appendChild(newLink);
        });

        // 提取script标签
        const scripts = Array.from(tempDiv.querySelectorAll('script'));

        // 移除原有的script标签，避免重复
        scripts.forEach(script => script.remove());

        // 将处理后的HTML内容插入目标容器
        target.innerHTML = tempDiv.innerHTML;

        if (scripts) {
            await admin().executeScriptsSequentially(scripts);
        }

        // 拦截都有的 target 内的 a 标签
        const anchors = target.querySelectorAll('a');
        for (const anchor of anchors) {
            anchor.addEventListener('click', function (e) {
                if (anchor.target !== '_blank') {
                    e.preventDefault();
                    admin().loadContent(anchor.href, target);
                }
            });
        }

        // 拦截表单提交
        const forms = target.querySelectorAll('form');
        for (const form of forms) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(form);
                const url = new URL(form.action);
                console.log(formData);
                admin().loadContent(url.toString(), target, formData);
            });
        }
    }

    async loadRenderable(target) {
        const renderable = target.getAttribute('data-renderable');
        const payload = target.getAttribute('data-payload');

        if (!renderable) {
            return;
        }
        const params = {
            renderable,
            payload
        }
        const urlParams = btoa(JSON.stringify(params));

        await admin().loadContent(`__render/${urlParams}`, target);
    }

    executeScript(script) {
        return new Promise((resolve, reject) => {
            const newScript = document.createElement('script');

            // 复制脚本属性
            Array.from(script.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });

            // 处理内联脚本
            if (script.textContent) {
                newScript.textContent = script.textContent;
                document.head.appendChild(newScript);
                resolve();
            }
            // 处理外部脚本
            else if (script.src) {
                newScript.onload = () => resolve();
                newScript.onerror = () => reject(new Error(`Failed to load script: ${script.src}`));
                document.head.appendChild(newScript);
            } else {
                resolve();
            }
        });
    }

    onLazyTriggerClick(e) {
        const target_id = e.target.getAttribute('data-lazy-target');
        const target = document.getElementById(target_id);
        admin().loadRenderable(target);
    }

    initActions(container) {
        document.querySelectorAll(container + " [data-action]").forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                console.log('clicked');
                let button = e.currentTarget;
                const confirm_title = button.getAttribute('data-confirm');
                if (confirm_title && !confirm(confirm_title)) {
                    return;
                }
                const action = button.getAttribute('data-action');
                button.classList.add('disabled');
                admin().post(action, {}, function () {
                    button.classList.remove('disabled');
                }, function (error) {
                    console.log('fail');
                });
            });
        })
    }
}

class SelectTable {
    constructor(options) {
        let that = this;

        that.id = options.id;
        that.url = options.url;
        that.nonce = options.nonce;
        that.selected = [];
        that.choices = new Choices("#" + that.id + ' .form-select', {
            removeItemButton: true,
        });
        that.button_select = $('#' + that.id + ' .btn-select');
        that.button_clear = $('#' + that.id + ' .btn-clear');
        that.dialog = $('.select-table-dialog[data-table=' + that.id + ']');

        that.button_clear.on('click', function () {
            that.selected = [];
            that.choices.clearChoices();
            that.choices.clearStore();
            that.button_clear.hide();
        });

        that.button_select.on('click', function () {
            that.asyncLoad({
                element: that.dialog.data('from'), nonce: that.nonce,
            }, that.dialog.find('.modal-body'));
            that.dialog.modal('show');
        });

        that.dialog.find('button.btn-confirm').on('click', function () {
            let remove_select = [];
            that.dialog.find('.check:checked:not(.selected)').each(function (index, item) {
                let v = $(item).data('id') + "";
                that.selected.push({
                    value: v, label: $(item).data('label')
                })
            });

            that.dialog.find('.check:not(:checked).selected').each(function (index, item) {
                remove_select.push($(item).data('id') + "");
            });

            // remove remove_select values from selected
            that.selected = that.selected.filter(item => !remove_select.includes(item.value));

            that.choices.clearChoices();
            that.choices.clearStore();
            that.choices.setValue(that.selected);
            that.dialog.modal('hide');

            if (that.selected.length > 0) {
                that.button_clear.show();
            } else {
                that.button_clear.hide();
            }
        });

        that.button_clear.hide();

        $('#' + that.id).removeClass('hidden');
    }

    asyncLoad(params, container) {
        let that = this;

        admin().asyncRender(this.url, params, function (data) {
            $(container).html(data);

            that.selected = [];
            let _values = that.choices.getValue();

            for (let i in _values) {
                let _value = _values[i];
                that.selected.push({
                    value: _value.value, label: _value.label
                })
            }
            for (let i in that.selected) {
                let id = that.selected[i]['value'];
                $(container).find('.check[data-id=' + id + ']').prop('checked', true);
                $(container).find('.check[data-id=' + id + ']').addClass('selected');
            }

            $(container).find('a').on("click", function (e) {
                e.preventDefault();
                let _params = admin().params(this.href);
                _params.element = params.element;
                _params._nonce = "{{admin()->csp()}}";
                that.asyncLoad(_params, container);
            });

            $(container).find('.check').on("change", function () {
                console.log($(this).data('id') + ' : ' + $(this).prop('checked'));
            });

            $(container).find('form').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const _params = {};
                formData.forEach((value, key) => {
                    _params[key] = value;
                });
                _params.element = params.element;
                _params._nonce = "{{admin()->csp()}}";
                that.asyncLoad(_params, container);
                return false;
            });
        });
    }
}

class LazyForm {

    rendered = false;
    contentUrl;
    id;

    constructor(options) {
        let that = this;
        that.id = options.id;
        that.contentUrl = options.url;
        document.getElementById('btn_' + that.id).addEventListener('click', function () {
            if (!that.rendered) {
                const container = document.getElementById('lazy_content_' + that.id);
                admin().asyncRender(that.contentUrl, function (result) {
                    $('#errors_' + that.id).hide();
                    container.innerHTML = result;
                    container.querySelectorAll('form').forEach(function (form) {
                        form.addEventListener('submit', function (e) {
                            e.preventDefault();
                            $.ajax({
                                type: 'post',
                                url: form.getAttribute('action'),
                                data: new FormData(form),
                                processData: false,
                                contentType: false,
                                success: function (result) {
                                    admin().response(result);
                                },
                                error: function (error) {
                                    let data = error.responseJSON;
                                    admin().errors(data.errors, container);
                                    $('#errors_' + that.id).html(data.message).show();
                                }
                            })
                        });
                    });
                });
                that.rendered = true;
            }
            (new bootstrap.Modal(document.getElementById(that.id))).show();
        });
    }
}

window.admin = function () {
    return Merlion.make();
}
window.admin();
