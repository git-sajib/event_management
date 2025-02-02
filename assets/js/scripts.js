document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);

    // Show success toast for registration
    if (urlParams.has('success')) {
        const toastElement = document.getElementById('successToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }

    // Show edit toast for update actions
    if (urlParams.has('edit')) {
        const toastElement = document.getElementById('editToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }

    // Show delete toast for delete actions
    if (urlParams.has('delete')) {
        const toastElement = document.getElementById('deleteToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }

    // Show error toast for capacity exceeded
    if (urlParams.has('error')) {
        const toastElement = document.getElementById('errorToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }

    // Show register success toast for attendee registration
    if (urlParams.has('register_success')) {
        const toastElement = document.getElementById('registerSuccessToast');
        if (toastElement) {
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    }
});