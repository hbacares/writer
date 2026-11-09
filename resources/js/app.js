import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import 'trix';

window.Alpine = Alpine;
window.Sortable = Sortable;

Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    initSortable('chapters-list', '/stories/:storyId/reorder-chapters', 'chapters');
    initSortable('sections-list', '/chapters/:chapterId/reorder-sections', 'sections');
    initSortable('paragraphs-list', '/sections/:sectionId/reorder-paragraphs', 'paragraphs');
});

function initSortable(elementId, urlPattern, itemsKey) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const parentId = element.dataset.parentId;
    if (!parentId) return;

    const url = urlPattern.replace(/:(\w+)Id/, parentId);

    Sortable.create(element, {
        animation: 150,
        handle: '.drag-handle',
        ghostClass: 'opacity-50',
        onEnd: function(evt) {
            const items = Array.from(element.children).map(child => child.dataset.id);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    [itemsKey]: items
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Reordered successfully');
            })
            .catch(error => {
                console.error('Error reordering:', error);
                location.reload();
            });
        }
    });
}
