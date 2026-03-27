// Mobile menu
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

// Close user dropdown (details) when clicking outside
document.addEventListener('click', (e) => {
    const userMenu = document.getElementById('userMenu');
    const details = userMenu && userMenu.querySelector('details');
    if (userMenu && details && !userMenu.contains(e.target)) {
        details.removeAttribute('open');
    }
});

// Auto-dismiss flash messages + PWA registration
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[role="alert"]').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s, transform 0.5s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(() => {
            // ignore registration errors
        });
    }

    const navBadgesUrl = document.body.getAttribute('data-nav-badges');
    if (navBadgesUrl) {
        const badgeNotifHeader = document.getElementById('nav-notif-badge-header');
        const badgeNotifBottom = document.getElementById('nav-notif-badge-bottom-orders');
        const badgeChatHeader = document.getElementById('nav-unread-badge');
        const badgeChatBottom = document.getElementById('nav-unread-badge-bottom');

        const updateCountBadge = (el, count) => {
            if (!el) return;
            const label = count > 99 ? '99+' : String(Math.max(0, count));
            const show = count > 0;
            if (show) {
                el.textContent = label;
                el.classList.remove('hidden');
            } else {
                el.textContent = '';
                el.classList.add('hidden');
            }
        };

        const storageKey = 'inwork_push_seen_notif_ids';
        const getSeenIds = () => {
            try {
                const raw = sessionStorage.getItem(storageKey);
                const arr = raw ? JSON.parse(raw) : [];
                return Array.isArray(arr) ? arr : [];
            } catch {
                return [];
            }
        };
        const addSeenId = (id) => {
            const seen = getSeenIds();
            if (!seen.includes(id)) {
                seen.push(id);
                while (seen.length > 100) seen.shift();
                sessionStorage.setItem(storageKey, JSON.stringify(seen));
            }
        };

        /** Типы уведомлений для системного пуша: «Мои заказы», чат, оплата, отзывы */
        const pushTypeMeta = {
            bid_accepted: { title: 'Отклик принят' },
            new_bid: { title: 'Новый отклик на заказ' },
            work_delivered: { title: 'Работа сдана' },
            order_completed: { title: 'Заказ завершён' },
            payment: { title: 'Оплата получена' },
            order_cancelled: { title: 'Заказ отменён' },
            bid_rejected: { title: 'Отклик отклонён' },
            refund: { title: 'Возврат средств' },
            new_review: { title: 'Новый отзыв' },
        };

        const showPushForNotification = (n) => {
            if (!('Notification' in window)) return;
            const meta = pushTypeMeta[n.type];
            if (!meta) return;

            const id = Number(n.id);
            if (!id || getSeenIds().includes(id)) return;
            if (Notification.permission !== 'granted') return;

            const title = meta.title;
            const body = n.message || '';
            let target = n.link || '/my-orders';
            if (target.startsWith('/')) {
                target = window.location.origin + target;
            }

            const note = new Notification(title, {
                body,
                tag: `${n.type}_${id}`,
                renotify: false,
            });
            addSeenId(id);
            note.onclick = () => {
                window.focus();
                window.location.href = target;
                note.close();
            };
        };

        const pollNavBadges = async () => {
            try {
                const resp = await fetch(navBadgesUrl, { credentials: 'same-origin' });
                if (!resp.ok) return;
                const data = await resp.json();

                const notifCount = typeof data.unread_notifications === 'number'
                    ? data.unread_notifications
                    : 0;
                const msgCount = typeof data.unread_messages === 'number'
                    ? data.unread_messages
                    : 0;

                updateCountBadge(badgeNotifHeader, notifCount);
                updateCountBadge(badgeNotifBottom, notifCount);
                updateCountBadge(badgeChatHeader, msgCount);
                updateCountBadge(badgeChatBottom, msgCount);

                const list = data.notifications || [];
                if ('Notification' in window) {
                    for (let i = list.length - 1; i >= 0; i--) {
                        showPushForNotification(list[i]);
                    }
                }
            } catch {
                // ignore
            }
        };

        pollNavBadges();
        setInterval(pollNavBadges, 45000);

        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
});

// Format currency input
document.querySelectorAll('input[type="number"][step="0.01"]').forEach(input => {
    input.addEventListener('blur', function() {
        if (this.value && !isNaN(this.value)) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
});
