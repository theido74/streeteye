(function () {
    function formatUtcPlus2(date) {
        return new Intl.DateTimeFormat('fr-FR', {
            timeZone: 'Etc/GMT-2',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).format(date) + ' UTC+2';
    }

    function updateClockElements() {
        const now = new Date();
        document.querySelectorAll('[data-clock="utc2"]').forEach(function (element) {
            element.textContent = formatUtcPlus2(now);
        });
    }

    window.StreetEyeClock = {
        formatUtcPlus2: formatUtcPlus2,
        updateClockElements: updateClockElements
    };

    document.addEventListener('DOMContentLoaded', function () {
        updateClockElements();
        setInterval(updateClockElements, 1000);
    });
})();
