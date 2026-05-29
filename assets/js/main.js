document.addEventListener('DOMContentLoaded', function () {

    // Auto-dismiss flash messages after 5 s
    const flash = document.querySelector('.flash-message');
    if (flash) {
        setTimeout(function () {
            flash.style.opacity = '0';
            setTimeout(function () { flash.remove(); }, 500);
        }, 5000);
    }

    // Force uppercase on account number input (transfer page)
    const accInput = document.querySelector('input[name="to_account"]');
    if (accInput) {
        accInput.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    }

    // Prevent negative numbers in amount inputs
    document.querySelectorAll('input[type="number"]').forEach(function (input) {
        input.addEventListener('input', function () {
            if (parseFloat(this.value) < 0) this.value = '';
        });
    });

    // Confirm before destructive form submits (data-confirm attribute)
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm(form.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

});
