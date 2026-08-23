{{--
    Éditeur WYSIWYG Tiptap (chargé en modules ES depuis un CDN — pas de bundler dans ce projet,
    même précédent que FullCalendar sur resources/views/commercial/agenda.blade.php).

    Attend les variables :
      - $hiddenInputId : id du <textarea> caché qui reçoit le HTML généré (doit déjà exister dans le DOM)
      - $groupesVariables : tableau groupé chemin => libellé, cf. DocumentVariableRegistry::groupes()
      - $editorId (optionnel) : id du conteneur d'édition, défaut "tiptapEditor"
--}}
@php($editorId = $editorId ?? 'tiptapEditor')

<div class="row g-4">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header border-bottom bg-light-subtle p-2">
                <div class="d-flex flex-wrap align-items-center gap-1" id="tiptapToolbar-{{ $editorId }}">
                    <button type="button" class="btn btn-sm btn-light" data-cmd="bold" title="Gras"><i class="bi bi-type-bold"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="italic" title="Italique"><i class="bi bi-type-italic"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="underline" title="Souligné"><i class="bi bi-type-underline"></i></button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="h1" title="Titre 1">H1</button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="h2" title="Titre 2">H2</button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="h3" title="Titre 3">H3</button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="paragraph" title="Paragraphe"><i class="bi bi-paragraph"></i></button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="bulletList" title="Liste à puces"><i class="bi bi-list-ul"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="orderedList" title="Liste numérotée"><i class="bi bi-list-ol"></i></button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="alignLeft" title="Aligner à gauche"><i class="bi bi-text-left"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="alignCenter" title="Centrer"><i class="bi bi-text-center"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="alignRight" title="Aligner à droite"><i class="bi bi-text-right"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="alignJustify" title="Justifier"><i class="bi bi-justify"></i></button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="table" title="Insérer un tableau"><i class="bi bi-table"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="hr" title="Ligne horizontale"><i class="bi bi-hr"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="link" title="Insérer un lien"><i class="bi bi-link-45deg"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="unlink" title="Retirer le lien"><i class="bi bi-link-45deg"></i><i class="bi bi-x fs-11"></i></button>
                    <span class="vr mx-1"></span>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="undo" title="Annuler"><i class="bi bi-arrow-counterclockwise"></i></button>
                    <button type="button" class="btn btn-sm btn-light" data-cmd="redo" title="Rétablir"><i class="bi bi-arrow-clockwise"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="{{ $editorId }}" class="tiptap-editor-zone"></div>
            </div>
            <div class="card-footer bg-light-subtle py-2 px-3 d-flex justify-content-between fs-12 text-muted">
                <span id="tiptapStatus-{{ $editorId }}">Prêt.</span>
                <span id="tiptapCount-{{ $editorId }}">0 caractère(s)</span>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="bi bi-braces me-1"></i>Variables</h6></div>
            <div class="card-body p-0" style="max-height: 620px; overflow-y: auto;">
                <div class="accordion accordion-flush" id="accordionVariables-{{ $editorId }}">
                    @foreach ($groupesVariables as $groupe => $variables)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ ! $loop->first ? 'collapsed' : '' }} fs-13 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#varGroupe-{{ $editorId }}-{{ $loop->index }}">
                                    {{ $groupe }}
                                </button>
                            </h2>
                            <div id="varGroupe-{{ $editorId }}-{{ $loop->index }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionVariables-{{ $editorId }}">
                                <div class="accordion-body p-2">
                                    <div class="list-group list-group-flush">
                                        @foreach ($variables as $chemin => $libelle)
                                            @php($apercuVariable = '{{ '.$chemin.' }}')
                                            <button type="button" class="list-group-item list-group-item-action py-1 px-2 fs-12 tiptap-insert-variable" data-editor="{{ $editorId }}" data-path="{{ $chemin }}">
                                                <span class="fw-medium">{{ $libelle }}</span>
                                                <br><code class="fs-10">{{ $apercuVariable }}</code>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer fs-11 text-muted">
                <i class="bi bi-info-circle me-1"></i>Cliquez sur une variable pour l'insérer au curseur.
            </div>
        </div>
    </div>
</div>

<style>
    .tiptap-editor-zone { min-height: 480px; padding: 20px 28px; }
    .tiptap-editor-zone .ProseMirror { outline: none; min-height: 460px; }
    .tiptap-editor-zone .ProseMirror table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    .tiptap-editor-zone .ProseMirror table td, .tiptap-editor-zone .ProseMirror table th { border: 1px solid #d8dfef; padding: 6px 8px; }
    .tiptap-editor-zone .ProseMirror th { background-color: #f5f7fc; }
    .tiptap-editor-zone .ProseMirror h1 { font-size: 1.7rem; }
    .tiptap-editor-zone .ProseMirror h2 { font-size: 1.4rem; }
    .tiptap-editor-zone .ProseMirror h3 { font-size: 1.15rem; }
    #tiptapToolbar-{{ $editorId }} .btn.is-active { background-color: #1a3c8c; color: #fff; }
</style>

<script type="module">
    import { Editor } from 'https://esm.sh/@@tiptap/core@2.6.6?bundle';
    import StarterKit from 'https://esm.sh/@@tiptap/starter-kit@2.6.6?bundle';
    import Underline from 'https://esm.sh/@@tiptap/extension-underline@2.6.6?bundle';
    import TextAlign from 'https://esm.sh/@@tiptap/extension-text-align@2.6.6?bundle';
    import Table from 'https://esm.sh/@@tiptap/extension-table@2.6.6?bundle';
    import TableRow from 'https://esm.sh/@@tiptap/extension-table-row@2.6.6?bundle';
    import TableHeader from 'https://esm.sh/@@tiptap/extension-table-header@2.6.6?bundle';
    import TableCell from 'https://esm.sh/@@tiptap/extension-table-cell@2.6.6?bundle';
    import Link from 'https://esm.sh/@@tiptap/extension-link@2.6.6?bundle';

    (function () {
        const editorId = {!! json_encode($editorId) !!};
        const hiddenInput = document.getElementById({!! json_encode($hiddenInputId) !!});
        const mountEl = document.getElementById(editorId);
        const statusEl = document.getElementById('tiptapStatus-' + editorId);
        const countEl = document.getElementById('tiptapCount-' + editorId);
        const toolbar = document.getElementById('tiptapToolbar-' + editorId);

        if (! mountEl || ! hiddenInput) {
            return;
        }

        const editor = new Editor({
            element: mountEl,
            extensions: [
                StarterKit,
                Underline,
                TextAlign.configure({ types: ['heading', 'paragraph'] }),
                Table.configure({ resizable: true }),
                TableRow,
                TableHeader,
                TableCell,
                Link.configure({ openOnClick: false }),
            ],
            content: hiddenInput.value || '<p></p>',
            onUpdate: ({ editor }) => {
                hiddenInput.value = editor.getHTML();
                rafraichirEtat();
            },
            onSelectionUpdate: () => rafraichirEtat(),
        });

        window['tiptapEditor_' + editorId] = editor;

        function libelleBloc() {
            if (editor.isActive('heading', { level: 1 })) return 'Titre 1';
            if (editor.isActive('heading', { level: 2 })) return 'Titre 2';
            if (editor.isActive('heading', { level: 3 })) return 'Titre 3';
            if (editor.isActive('bulletList')) return 'Liste à puces';
            if (editor.isActive('orderedList')) return 'Liste numérotée';
            if (editor.isActive('table')) return 'Tableau';
            return 'Paragraphe';
        }

        function rafraichirEtat() {
            const marques = [];
            if (editor.isActive('bold')) marques.push('gras');
            if (editor.isActive('italic')) marques.push('italique');
            if (editor.isActive('underline')) marques.push('souligné');
            if (editor.isActive('link')) marques.push('lien');

            statusEl.textContent = libelleBloc() + (marques.length ? ' — ' + marques.join(', ') : '');
            countEl.textContent = editor.getText().length + ' caractère(s)';

            if (toolbar) {
                toolbar.querySelectorAll('[data-cmd]').forEach((btn) => {
                    const cmd = btn.dataset.cmd;
                    let actif = false;
                    if (cmd === 'bold') actif = editor.isActive('bold');
                    else if (cmd === 'italic') actif = editor.isActive('italic');
                    else if (cmd === 'underline') actif = editor.isActive('underline');
                    else if (cmd === 'h1') actif = editor.isActive('heading', { level: 1 });
                    else if (cmd === 'h2') actif = editor.isActive('heading', { level: 2 });
                    else if (cmd === 'h3') actif = editor.isActive('heading', { level: 3 });
                    else if (cmd === 'paragraph') actif = editor.isActive('paragraph');
                    else if (cmd === 'bulletList') actif = editor.isActive('bulletList');
                    else if (cmd === 'orderedList') actif = editor.isActive('orderedList');
                    else if (cmd === 'alignLeft') actif = editor.isActive({ textAlign: 'left' });
                    else if (cmd === 'alignCenter') actif = editor.isActive({ textAlign: 'center' });
                    else if (cmd === 'alignRight') actif = editor.isActive({ textAlign: 'right' });
                    else if (cmd === 'alignJustify') actif = editor.isActive({ textAlign: 'justify' });
                    btn.classList.toggle('is-active', actif);
                });
            }
        }

        if (toolbar) {
            toolbar.querySelectorAll('[data-cmd]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const chain = editor.chain().focus();
                    switch (btn.dataset.cmd) {
                        case 'bold': chain.toggleBold().run(); break;
                        case 'italic': chain.toggleItalic().run(); break;
                        case 'underline': chain.toggleUnderline().run(); break;
                        case 'h1': chain.toggleHeading({ level: 1 }).run(); break;
                        case 'h2': chain.toggleHeading({ level: 2 }).run(); break;
                        case 'h3': chain.toggleHeading({ level: 3 }).run(); break;
                        case 'paragraph': chain.setParagraph().run(); break;
                        case 'bulletList': chain.toggleBulletList().run(); break;
                        case 'orderedList': chain.toggleOrderedList().run(); break;
                        case 'alignLeft': chain.setTextAlign('left').run(); break;
                        case 'alignCenter': chain.setTextAlign('center').run(); break;
                        case 'alignRight': chain.setTextAlign('right').run(); break;
                        case 'alignJustify': chain.setTextAlign('justify').run(); break;
                        case 'table': chain.insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(); break;
                        case 'hr': chain.setHorizontalRule().run(); break;
                        case 'link': {
                            const url = window.prompt('URL du lien :');
                            if (url) chain.extendMarkRange('link').setLink({ href: url }).run();
                            break;
                        }
                        case 'unlink': chain.unsetLink().run(); break;
                        case 'undo': chain.undo().run(); break;
                        case 'redo': chain.redo().run(); break;
                    }
                    rafraichirEtat();
                });
            });
        }

        document.querySelectorAll('.tiptap-insert-variable[data-editor="' + editorId + '"]').forEach((item) => {
            item.addEventListener('click', () => {
                // Construit le texte double-accolade par concaténation pour ne jamais faire
                // apparaître la séquence brute dans ce fichier Blade (sinon le compilateur
                // Blade tente de l'interpréter comme une expression PHP).
                var ouvrant = '{' + '{';
                var fermant = '}' + '}';
                editor.chain().focus().insertContent(ouvrant + ' ' + item.dataset.path + ' ' + fermant).run();
            });
        });

        rafraichirEtat();

        // Rendre le contenu HTML disponible pour la soumission classique du formulaire englobant.
        const formulaire = hiddenInput.closest('form');
        if (formulaire) {
            formulaire.addEventListener('submit', () => {
                hiddenInput.value = editor.getHTML();
            });
        }
    })();
</script>
