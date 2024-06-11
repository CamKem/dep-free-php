<div class="pagination">
    <?php if ($items->previousPageUrl()): ?>
        <a class="pagination-link"
           href="<?= $items->previousPageUrl() ?>"
        >Prev</a>
    <?php else: ?>
        <p class="pagination-disabled">Prev</p>
    <?php endif; ?>
    <?php foreach ($items->links() as $index => $link): ?>
        <a class="<?= $items->currentPage() === $index + 1 ? 'pagination-active' : 'pagination-link' ?>"
           href="<?= $link ?>">
            <?= $index + 1 ?>
        </a>
    <?php endforeach; ?>
    <?php if ($items->nextPageUrl()): ?>
        <a class="pagination-link"
           href="<?= $items->nextPageUrl() ?>"
        >Next</a>
    <?php else: ?>
        <p class="pagination-disabled">Next</p>
    <?php endif; ?>
</div>