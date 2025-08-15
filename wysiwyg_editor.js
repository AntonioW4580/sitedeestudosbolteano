// Editor WYSIWYG simples
document.addEventListener('DOMContentLoaded', function() {
    // Se tiver elementos com a classe 'wysiwyg'
    const wysiwygElements = document.querySelectorAll('.wysiwyg-editor');
    
    wysiwygElements.forEach(function(element) {
        // Criar toolbar
        const toolbar = document.createElement('div');
        toolbar.className = 'wysiwyg-toolbar';
        toolbar.innerHTML = `
            <button onclick="formatText('bold')" title="Negrito">B</button>
            <button onclick="formatText('italic')" title="Itálico">I</button>
            <button onclick="formatText('underline')" title="Sublinhado">U</button>
            <button onclick="insertLink()" title="Inserir Link">🔗</button>
            <button onclick="insertImage()" title="Inserir Imagem">🖼️</button>
        `;
        
        element.parentNode.insertBefore(toolbar, element);
    });
});

function formatText(command) {
    document.execCommand(command, false, null);
}

function insertLink() {
    const url = prompt('Digite a URL:');
    if (url) {
        document.execCommand('createLink', false, url);
    }
}

function insertImage() {
    const url = prompt('Digite a URL da imagem:');
    if (url) {
        document.execCommand('insertImage', false, url);
    }
}