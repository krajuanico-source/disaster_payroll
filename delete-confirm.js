/**
 * Shared delete-confirmation flow for the ECT Served List page.
 * Pairs with delete-confirm-modal.html and delete_served.php.
 *
 * Requires jQuery + Bootstrap 5 modal JS to already be loaded.
 *
 * Public API:
 *   confirmAndDelete({ mode: 'selected', ids: [1,2,3] })
 *   confirmAndDelete({ mode: 'date', date_from: '2026-01-01', date_to: '2026-01-31' })
 *   confirmAndDelete({ mode: 'all' })
 *
 * Define this yourself elsewhere to react on success (e.g. reload DataTable):
 *   function onDeleteSuccess(response) { table.ajax.reload(); }
 */

let _pendingDeletePayload = null;
let _deleteModalInstance = null;

function _getDeleteModal() {
    if (!_deleteModalInstance) {
        const el = document.getElementById('deleteConfirmModal');
        _deleteModalInstance = bootstrap.Modal.getOrCreateInstance(el);
    }
    return _deleteModalInstance;
}

function _summaryTextFor(payload) {
    switch (payload.mode) {
        case 'selected': {
            const n = (payload.ids || []).length;
            return n === 1
                ? 'You are about to delete 1 selected record.'
                : `You are about to delete ${n} selected records.`;
        }
        case 'date':
            return `You are about to delete all records with last-served date between ${payload.date_from} and ${payload.date_to}.`;
        case 'all':
            return 'You are about to delete ALL records in the Served List. This cannot be undone from the UI.';
        default:
            return 'You are about to delete records.';
    }
}

/**
 * Call this instead of posting to delete_served.php directly.
 * Opens the confirmation modal; the actual AJAX call happens on submit.
 */
function confirmAndDelete(payload) {
    _pendingDeletePayload = payload;

    $('#deleteConfirmSummary').text(_summaryTextFor(payload));
    $('#deleteConfirmPassword').val('');
    $('#deleteConfirmError').hide().text('');

    _getDeleteModal().show();

    // Focus the password field once the modal is visible
    $('#deleteConfirmModal').one('shown.bs.modal', function () {
        $('#deleteConfirmPassword').trigger('focus');
    });
}

$(function () {
    $('#deleteConfirmForm').on('submit', function (e) {
        e.preventDefault();

        if (!_pendingDeletePayload) {
            return;
        }

        const password = $('#deleteConfirmPassword').val();
        if (!password) {
            $('#deleteConfirmError').text('Password is required.').show();
            return;
        }

        const postData = Object.assign({}, _pendingDeletePayload, { password: password });

        const $submitBtn = $('#deleteConfirmSubmitBtn');
        const $spinner = $('#deleteConfirmSpinner');
        $submitBtn.prop('disabled', true);
        $spinner.show();
        $('#deleteConfirmError').hide().text('');

        $.post('delete_served.php', postData)
            .done(function (response) {
                if (response && response.success) {
                    _getDeleteModal().hide();
                    _pendingDeletePayload = null;

                    if (typeof onDeleteSuccess === 'function') {
                        onDeleteSuccess(response);
                    } else {
                        alert(response.message || 'Records deleted successfully.');
                    }
                } else {
                    $('#deleteConfirmError')
                        .text((response && response.message) || 'Delete failed.')
                        .show();
                }
            })
            .fail(function (xhr) {
                let msg = 'Server error. Please try again.';
                try {
                    const parsed = JSON.parse(xhr.responseText);
                    if (parsed && parsed.message) {
                        msg = parsed.message;
                    }
                } catch (err) {
                    // ignore parse failure, use default msg
                }
                $('#deleteConfirmError').text(msg).show();
            })
            .always(function () {
                $submitBtn.prop('disabled', false);
                $spinner.hide();
            });
    });

    // Reset state if the modal is dismissed without confirming
    $('#deleteConfirmModal').on('hidden.bs.modal', function () {
        _pendingDeletePayload = null;
        $('#deleteConfirmPassword').val('');
        $('#deleteConfirmError').hide().text('');
    });
});