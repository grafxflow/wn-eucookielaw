document.addEventListener("DOMContentLoaded", function() {
    (function () {
        const cfg = window.CookieNoticeConfig || {};

        window.cookieconsent.initialise({
            content: {
                "header": cfg.header,
                "message": cfg.message,
                "dismiss": cfg.dismiss,
                "allow": cfg.allow,
                "deny": cfg.deny,
                "link": cfg.link,
                "href": cfg.href
            },
            "position": cfg.position,
            "container": cfg.container,
            "palette": cfg.palette || {},
            "theme": cfg.theme,
            "elements": cfg.elements || {},
            "compliance": cfg.compliance || {},
            "type": cfg.type,
            "revokable": cfg.revokable,
            "revokeBtn": cfg.revokeBtn,
            "static": cfg.static,
            "dismissOnScroll": cfg.dismissOnScroll,
            "dismissOnTimeout": cfg.dismissOnTimeout,

            "cookie.path": cfg.cookie_path,
            "cookie.name": cfg.cookie_name,
            "cookie.domain": cfg.cookie_domain,
            "cookie.expiryDays": cfg.cookie_expiryDays,

            onStatusChange: function(status, chosenBefore) {
                if (status === 'deny') {
                    clearAllCookiesExceptCookieNotice();
                }
                window.location.reload();
            }
        });

        function getCookieDomain() {
            let cookieDomain = cfg.cookie_domain || "";
            if (cookieDomain.trim() === "") {
                cookieDomain = window.location.hostname;
            }
            return cookieDomain;
        }

        function getCookiePath() {
            let cookiePath = cfg.cookie_path || "";
            if (cookiePath.trim() === "") {
                cookiePath = "/";
            }
            return cookiePath;
        }

        function clearAllCookiesExceptCookieNotice() {
            const cookies = document.cookie.split(";");
            const cookieDomain = getCookieDomain();
            const cookiePath = getCookiePath();

            for (let i = 0; i < cookies.length; i++) {
                const spcook = cookies[i].split("=");
                const cookieName = spcook[0].trim();
                if (cookieName !== cfg.cookie_name) {
                    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=" + cookieDomain + "; path=" + cookiePath;
                    document.cookie = cookieName + "=; expires=Thu, 01 Jan 1970 00:00:01 GMT; domain=." + cookieDomain + "; path=" + cookiePath;
                }
            }
        }
    })();
});
