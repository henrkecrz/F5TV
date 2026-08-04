<?php
/**
 * Template Name: Login
 * Design system F5TV — pixel-perfect com AuthPages.tsx / LoginScreen
 */

// Se já logado, redirecionar
if (is_user_logged_in()) {
    wp_redirect(home_url('/area-do-assinante/'));
    exit;
}

get_header();
?>

<div id="f5-login-root" style="min-height:100vh;background:#000;display:flex;align-items:center;justify-content:center;padding:24px;position:relative;font-family:'Inter',ui-sans-serif,system-ui,sans-serif;">

    <!-- Fundo cinematográfico (igual ao React: opacity-10) -->
    <div style="position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=1200') center/cover no-repeat;opacity:.1;pointer-events:none;"></div>

    <!-- Glow vermelho suave -->
    <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse at top,rgba(220,38,38,.12),transparent 70%);pointer-events:none;"></div>

    <!-- Card principal — bg-f5-blue-950 border border-zinc-900 -->
    <div style="width:100%;max-width:440px;background:#0a0a1a;border:1px solid #18181b;border-radius:16px;padding:32px;box-shadow:0 25px 60px rgba(0,0,0,.7);position:relative;z-index:10;">

        <!-- Logo + subtítulo -->
        <div style="display:flex;flex-direction:column;align-items:center;gap:8px;margin-bottom:28px;text-align:center;">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="text-decoration:none;">
                <span style="font-size:28px;font-weight:900;letter-spacing:-.04em;text-transform:uppercase;color:#fff;line-height:1;">
                    F5 <span style="color:#dc2626;">TV</span>
                </span>
            </a>
            <p style="color:#71717a;font-size:13px;font-weight:500;margin:0;">Faça login para entrar na área exclusiva.</p>
        </div>

        <!-- Mensagem de erro -->
        <?php
        $login_error = isset($_GET['login']) && $_GET['login'] === 'failed';
        $custom_error = isset($_GET['error']) ? sanitize_text_field($_GET['error']) : '';
        if ($login_error || $custom_error):
        ?>
        <div style="margin-bottom:20px;padding:12px 14px;background:rgba(153,27,27,.25);border:1px solid #7f1d1d;border-radius:8px;display:flex;align-items:flex-start;gap:10px;">
            <svg style="width:16px;height:16px;color:#f87171;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"/>
            </svg>
            <span style="color:#fca5a5;font-size:12px;line-height:1.5;">
                <?php echo $custom_error ?: 'E-mail ou senha incorretos. Tente novamente.'; ?>
            </span>
        </div>
        <?php endif; ?>

        <!-- Mensagem de sucesso (ex: após reset de senha) -->
        <?php if (isset($_GET['checkemail'])): ?>
        <div style="margin-bottom:20px;padding:12px 14px;background:rgba(5,46,22,.4);border:1px solid #166534;border-radius:8px;">
            <span style="color:#86efac;font-size:12px;">Verifique seu e-mail para redefinir a senha.</span>
        </div>
        <?php endif; ?>

        <!-- Formulário -->
        <form action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post" id="f5-login-form" style="display:flex;flex-direction:column;gap:18px;">

            <!-- Campo: E-mail -->
            <div style="display:flex;flex-direction:column;gap:6px;">
                <label for="f5-email" style="font-size:11px;font-family:monospace;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;">
                    E-mail corporativo ou assinante
                </label>
                <div style="position:relative;">
                    <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#52525b;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input
                        type="text"
                        name="log"
                        id="f5-email"
                        placeholder="nome@f5tv.com.br ou seu@email.com"
                        autocomplete="username"
                        required
                        style="width:100%;background:#0d0d2a;border:1px solid #27272a;border-radius:8px;padding:10px 14px 10px 40px;font-size:14px;color:#f4f4f5;outline:none;transition:border-color .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#27272a'">
                </div>
            </div>

            <!-- Campo: Senha -->
            <div style="display:flex;flex-direction:column;gap:6px;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <label for="f5-password" style="font-size:11px;font-family:monospace;font-weight:700;color:#a1a1aa;text-transform:uppercase;letter-spacing:.08em;">
                        Sua senha tática
                    </label>
                    <a href="<?php echo esc_url(wp_lostpassword_url(get_permalink())); ?>"
                       style="font-size:12px;color:#dc2626;text-decoration:none;"
                       onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        Esqueceu?
                    </a>
                </div>
                <div style="position:relative;">
                    <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#52525b;pointer-events:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <input
                        type="password"
                        name="pwd"
                        id="f5-password"
                        placeholder="••••••••"
                        autocomplete="current-password"
                        required
                        style="width:100%;background:#0d0d2a;border:1px solid #27272a;border-radius:8px;padding:10px 40px 10px 40px;font-size:14px;color:#f4f4f5;outline:none;transition:border-color .2s;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#27272a'">
                    <!-- Toggle show/hide password -->
                    <button type="button" id="f5-pwd-toggle"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:0;color:#52525b;"
                            aria-label="Mostrar senha">
                        <svg id="f5-eye-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Lembrar + Submit -->
            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:-4px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:#71717a;">
                    <input type="checkbox" name="rememberme" value="forever"
                           style="width:14px;height:14px;accent-color:#dc2626;cursor:pointer;">
                    Lembrar-me
                </label>
            </div>

            <button type="submit" name="wp-submit" id="f5-login-submit"
                    style="width:100%;background:#dc2626;color:#fff;font-weight:700;font-size:12px;font-family:monospace;text-transform:uppercase;letter-spacing:.1em;padding:12px;border:none;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .2s,box-shadow .2s;box-shadow:0 4px 20px rgba(220,38,38,.3);"
                    onmouseover="this.style.background='#b91c1c';this.style.boxShadow='0 4px 28px rgba(220,38,38,.45)'"
                    onmouseout="this.style.background='#dc2626';this.style.boxShadow='0 4px 20px rgba(220,38,38,.3)'">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Entrar
            </button>

            <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/area-do-assinante/')); ?>">
            <input type="hidden" name="testcookie" value="1">
        </form>

        <!-- Divisor -->
        <div style="margin:24px 0;display:flex;align-items:center;gap:12px;">
            <div style="flex:1;height:1px;background:#18181b;"></div>
            <span style="font-size:11px;color:#3f3f46;font-family:monospace;letter-spacing:.05em;">OU</span>
            <div style="flex:1;height:1px;background:#18181b;"></div>
        </div>

        <!-- Link cadastro -->
        <p style="text-align:center;font-size:13px;color:#52525b;margin:0;">
            Ainda não tem conta?
            <a href="<?php echo esc_url(home_url('/cadastro/')); ?>"
               style="color:#dc2626;font-weight:700;text-decoration:none;margin-left:4px;"
               onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                Assine agora
            </a>
        </p>

    </div>
</div>

<script>
// Toggle show/hide password
(function() {
    var toggle = document.getElementById('f5-pwd-toggle');
    var input  = document.getElementById('f5-password');
    var icon   = document.getElementById('f5-eye-icon');
    if (!toggle || !input) return;

    var EYE_ON  = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    var EYE_OFF = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';

    toggle.addEventListener('click', function() {
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = EYE_OFF;
            toggle.style.color = '#dc2626';
        } else {
            input.type = 'password';
            icon.innerHTML = EYE_ON;
            toggle.style.color = '#52525b';
        }
    });

    // Loading state on submit
    var form   = document.getElementById('f5-login-form');
    var submit = document.getElementById('f5-login-submit');
    if (form && submit) {
        form.addEventListener('submit', function() {
            submit.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="animation:f5spin .8s linear infinite"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg> A entrar...';
            submit.disabled = true;
        });
    }
})();
</script>

<style>
@keyframes f5spin { to { transform: rotate(360deg); } }
/* Remove webkit autofill yellow */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
    -webkit-box-shadow: 0 0 0 1000px #0d0d2a inset !important;
    -webkit-text-fill-color: #f4f4f5 !important;
    caret-color: #f4f4f5;
}
</style>

<?php get_footer(); ?>
