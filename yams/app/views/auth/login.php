<div class="container" id="loginSection">
    <div class="login-container">
        <div class="card login-card">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-swimmer swimming-icon"></i>
                    <h2 class="text-primary mb-0">Yei Yei Academy</h2>
                    <p class="text-muted">Sistema de Gestión</p>
                </div>
                <!-- app/views/auth/login.php -->
                <form id="loginForm" method="POST" action="index.php?route=auth/signin">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user me-2"></i>Usuario</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label"><i class="fas fa-lock me-2"></i>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</button>
                </form>
                <!-- /app/views/auth/login.php -->
                <div class="text-center mt-3">
                    <small class="text-muted">Demo: admin / admin</small>
                </div>
            </div>
        </div>
    </div>
</div>