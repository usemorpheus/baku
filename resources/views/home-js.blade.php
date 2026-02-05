
<script>
function checkAndNavigateToTasks() {
    // 检查用户是否已通过Telegram认证
    fetch('/tasks/verify-auth', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.authenticated) {
            // 如果已认证，直接重定向到任务页面
            window.location.href = '/tasks';
        } else {
            // 如果未认证，提示用户连接Telegram并重定向到说明页面
            const result = confirm('To access tasks, you need to connect with our Telegram bot first. Would you like to start a conversation with @baku_news_bot now?');
            if (result) {
                window.open('https://t.me/baku_news_bot', '_blank');
                alert('Please start a conversation with @baku_news_bot, then return to this page to access your tasks.');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Please connect with Telegram bot first. Start a conversation with @baku_news_bot.');
        window.open('https://t.me/baku_news_bot', '_blank');
    });
}
</script>