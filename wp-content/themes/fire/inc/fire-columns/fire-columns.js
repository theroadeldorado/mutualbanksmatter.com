(function () {
  tinymce.PluginManager.add('columns', function (editor, url) {
    // Add Button to Visual Editor Toolbar
    editor.addButton('TwoColumns', {
      title: 'Insert 2 Columns',
      cmd: 'two-columns',
      image: url + '/two-columns.png',
    });

    editor.addCommand('two-columns', function () {
      var selected_text = editor.selection.getContent({
        format: 'html',
      });

      // Create the column wrapper
      var open_wrapper = '<div data-columns class="fire-columns-2">';
      var close_wrapper = '</div><p><br></p>';
      var column_content = '';

      if (selected_text.length === 0) {
        // Add placeholder content if nothing is selected
        column_content = '<div class="column"><p>Column 1 content. Select this text and edit it.</p></div>' + '<div class="column"><p>Column 2 content. Select this text and edit it.</p></div>';
      } else {
        // If text is selected, we need to decide how to split it
        // For now, we'll just put all selected content in the first column and create an empty second column
        column_content = '<div class="column">' + selected_text + '</div>' + '<div class="column"><p>Column 2 content. Select this text and edit it.</p></div>';
      }

      var return_text = open_wrapper + column_content + close_wrapper;
      editor.execCommand('mceReplaceContent', false, return_text);
      return;
    });

    editor.addButton('ThreeColumns', {
      title: 'Insert 3 Columns',
      cmd: 'three-columns',
      image: url + '/three-columns.png',
    });

    editor.addCommand('three-columns', function () {
      var selected_text = editor.selection.getContent({
        format: 'html',
      });

      // Create the column wrapper
      var open_wrapper = '<div data-columns class="fire-columns-3">';
      var close_wrapper = '</div><p><br></p>';
      var column_content = '';

      if (selected_text.length === 0) {
        // Add placeholder content if nothing is selected
        column_content =
          '<div class="column"><p>Column 1 content. Select this text and edit it.</p></div>' +
          '<div class="column"><p>Column 2 content. Select this text and edit it.</p></div>' +
          '<div class="column"><p>Column 3 content. Select this text and edit it.</p></div>';
      } else {
        // If text is selected, we need to decide how to split it
        // For now, we'll just put all selected content in the first column and create empty second and third columns
        column_content =
          '<div class="column">' +
          selected_text +
          '</div>' +
          '<div class="column"><p>Column 2 content. Select this text and edit it.</p></div>' +
          '<div class="column"><p>Column 3 content. Select this text and edit it.</p></div>';
      }

      var return_text = open_wrapper + column_content + close_wrapper;
      editor.execCommand('mceReplaceContent', false, return_text);
      return;
    });
  });
})();
