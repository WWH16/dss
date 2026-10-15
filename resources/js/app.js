// CHANGED: removed the axios bootstrap import; every request uses fetch().
import './toast';

// ADDED: a wrapper with data-track-field="<program select id>" holds the track select. It stays disabled until a program is picked, lists only that program's optgroup, and clears a track from another program.
document.querySelectorAll('[data-track-field]').forEach(field => {
    const course = document.getElementById(field.dataset.trackField);
    const track = field.querySelector('select');
    const sync = () => {
        track.disabled = course.value === '';
        track.querySelectorAll('optgroup').forEach(group => {
            group.hidden = group.disabled = group.label !== course.value;
        });
        if (track.selectedOptions[0]?.parentElement.disabled) track.value = '';
    };
    course.addEventListener('change', sync);
    sync();
});
