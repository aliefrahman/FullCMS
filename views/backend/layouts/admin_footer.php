</div>
</main>

<!-- Lucide Icon Renderer -->
<script src="<?= PUBLIC_URL ?>/assets/lucide/dist/umd/lucide.min.js"></script>
<script>
    lucide.createIcons();
</script>
<!-- Quill Script & Init -->
<script src="<?= PUBLIC_URL ?>/assets/quill/dist/quill.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Tuliskan isi artikel Anda di sini secara interaktif...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'clean']
                ]
            }
        });

        // Sync Quill editor HTML with the hidden textarea
        quill.on('text-change', function () {
            document.getElementById('article-content').value = quill.getSemanticHTML();
        });

        // Ensure latest content is captured on form submission
        document.getElementById('article-form').addEventListener('submit', function () {
            document.getElementById('article-content').value = quill.getSemanticHTML();
        });
    });
</script>

</body>

</html>