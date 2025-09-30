            </main>
        </div>
    </div>

    <!-- 本地jQuery -->
    <script src="../assets/js/jquery.min.js"></script>
    
    <!-- 本地Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // 全局函数
        function confirmDelete(message = '确定删除吗？此操作不可恢复。') {
            return confirm(message);
        }
        
        function showLoading() {
            const overlay = document.createElement('div');
            overlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center';
            overlay.style.background = 'rgba(0,0,0,0.5)';
            overlay.style.zIndex = '9999';
            overlay.innerHTML = `
                <div class="text-center text-white">
                    <div class="spinner-border" role="status"></div>
                    <div class="mt-2">处理中...</div>
                </div>
            `;
            document.body.appendChild(overlay);
            return overlay;
        }
        
        function hideLoading(overlay) {
            if (overlay) overlay.remove();
        }
        
        // 自动隐藏警告消息
        document.addEventListener('DOMContentLoaded', function() {
            // 5秒后自动隐藏警告消息
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
            
            // 表单提交显示加载
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> 处理中...';
                        submitBtn.disabled = true;
                        
                        // 3秒后恢复（防止无限等待）
                        setTimeout(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }, 10000);
                    }
                });
            });
        });
    </script>
</body>
</html>