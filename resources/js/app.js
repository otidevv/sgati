import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import DataTable from 'datatables.net-dt';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import '../css/datatables-overrides.css';

window.Alpine = Alpine;
window.Swal   = Swal;
window.DataTable = DataTable;

Alpine.start();

// ── Helpers de tema (dark/light) ──────────────────────────────────────
const _swalTheme = () => document.documentElement.classList.contains('dark')
    ? { background: '#1e293b', color: '#f1f5f9', iconColor: undefined }
    : { background: '#ffffff', color: '#111827', iconColor: undefined };

// ── Toast (esquina superior derecha, auto-cierre) ─────────────────────
const _Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 4500,
    timerProgressBar: true,
    didOpen: (el) => {
        el.addEventListener('mouseenter', Swal.stopTimer);
        el.addEventListener('mouseleave', Swal.resumeTimer);
    },
});

window.sgToast = (icon, title) => {
    const t = _swalTheme();
    _Toast.fire({ icon, title, background: t.background, color: t.color });
};

// ── Confirmación de eliminación para forms ────────────────────────────
window.sgDeleteForm = (form, text) => {
    const t = _swalTheme();
    Swal.fire({
        title: '¿Eliminar?',
        text: text ?? 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        focusCancel: true,
        reverseButtons: true,
        background: t.background,
        color: t.color,
    }).then(r => { if (r.isConfirmed) form.submit(); });
};

// ── Confirmación genérica (devuelve Promise<boolean>) ─────────────────
window.sgConfirm = (options = {}) => {
    const t = _swalTheme();
    return Swal.fire({
        icon: 'question',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        background: t.background,
        color: t.color,
        ...options,
    }).then(r => r.isConfirmed);
};

// ── Mostrar flash messages de sesión al cargar la página ──────────────
document.addEventListener('DOMContentLoaded', () => {
    const m = window._flashMessages ?? {};
    if (m.success) sgToast('success', m.success);
    if (m.error)   sgToast('error',   m.error);
    if (m.warning) sgToast('warning', m.warning);
    if (m.info)    sgToast('info',    m.info);
});
