(function() {
  var numberOfRows;

  $.fn.onlyCharacter = function() {
    return this.keypress(function(e) {
      if (!/[0-9a-zA-Z-]/.test(String.fromCharCode(e.which))) {
        return false;
      }
    });
  };

  $.fn.onlyNumber = function() {
    return this.keypress(function(e) {
      if (!/[0-9]/.test(String.fromCharCode(e.which))) {
        return false;
      }
    });
  };

  $('.only-character').onlyCharacter();

  $('.only-number').onlyNumber();

  $('#tabNodetype a').click(function(e) {
    e.preventDefault();
    return $(this).tab('show');
  });

  $('.reload-page').click(function() {
    return window.location.reload();
  });

  $('#helper').click(function() {
    if ($(this).is(':checked')) {
      return $('#helperMessage').removeClass('hide');
    } else {
      return $('#helperMessage').addClass('hide');
    }
  });

  $('#createGroupAtInspector').click(function() {
    if ($(this).is(':checked')) {
      return $('#groupName').removeClass('hide');
    } else {
      return $('#groupName').addClass('hide');
    }
  });

  $('#propertyType').change(function() {
    var $self;
    $self = $(this);
    if ($self.val() === 'string') {
      return $('.inline-editable-container').removeClass('hide');
    } else {
      return $('.inline-editable-container').addClass('hide');
    }
  });

  $('#propertyIsInline').click(function() {
    if ($(this).is(':checked')) {
      $('#propertyIsInlinePlaceholder').removeClass('hide');
      $('#propertyEditors').addClass('hide');
      $('#propertyEditors').prop('selectedIndex', 0);
      return $('.editor-container').find('.editor').each(function() {
        $(this).addClass('hide');
        return $(this).val('');
      });
    } else {
      $('#propertyIsInlinePlaceholder').addClass('hide');
      $('#propertyEditorText').removeClass('hide');
      return $('#propertyEditors').removeClass('hide');
    }
  });

  $('#propertyEditors').change(function() {
    var $self;
    $self = $(this);
    $('.editor-container').find('.editor').each(function() {
      return $(this).addClass('hide');
    });
    if ($self.val() === 'default') {
      return $('#propertyEditorText').removeClass('hide');
    } else if ($self.val() === 'TYPO3.Neos/Inspector/Editors/TextAreaEditor') {
      return $('#propertyEditorTextAreaRow').removeClass('hide');
    } else if ($self.val() === 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor') {
      return $('#propertyEditorSelect').removeClass('hide');
    }
  });

  numberOfRows = 1;

  $('#newProperty').click(function() {
    var dataRow, dataRowSumbit, defaultValue, editor, inlineEditable, inlineEditablePlaceholder, label, name, newTableRow, propertyType, selectOptions, tdAction, tdDefaultValue, tdLabel, tdName, tdPropertyType, tdValidators, textPlaceholder, textareaRow, validators;
    name = $('#propertyName').val();
    label = $('#propertyLabel').val();
    if (name !== '' && label !== '') {
      dataRow = {
        'name': name,
        'defaultValue': '',
        'label': label,
        'validators': '',
        'type': {
          'editorType': '',
          'inlineEditable': {
            'isInlineEditable': '',
            'placeholder': ''
          },
          'editorText': {
            'placeholder': ''
          },
          'editorTextArea': {
            'rows': ''
          },
          'editorSelect': {
            'options': ''
          }
        }
      };
      newTableRow = '<tr id="' + numberOfRows + '">';
      newTableRow += '<td>' + numberOfRows + '</td>';
      tdName = '<td>' + name + '</td>';
      tdLabel = '<td>' + label + '</td>';
      newTableRow += tdName;
      propertyType = $('#propertyType').val();
      tdPropertyType = '<td>' + propertyType;
      inlineEditable = $('#propertyIsInline');
      inlineEditablePlaceholder = $('#propertyIsInlinePlaceholder').val();
      editor = $('#propertyEditors').val();
      textPlaceholder = $('#propertyEditorText').val();
      textareaRow = $('#propertyEditorTextAreaRow').val();
      selectOptions = $('#propertyEditorSelect').val();
      if (propertyType === 'string') {
        propertyType += ':';
        if (inlineEditable.is(':checked')) {
          dataRow.type.inlineEditable.isInlineEditable = true;
          dataRow.type.inlineEditable.placeholder = inlineEditablePlaceholder;
          tdPropertyType += '<br>   - Inline editable: ' + inlineEditablePlaceholder;
        } else {
          dataRow.type.editorType = editor;
          if (editor === 'default') {
            dataRow.type.editorText.placeholder = textPlaceholder;
            tdPropertyType += '<br>   - Default text: ' + textPlaceholder;
          } else if (editor === 'TYPO3.Neos/Inspector/Editors/TextAreaEditor') {
            dataRow.type.editorTextArea.rows = textareaRow;
            tdPropertyType += '<br> Text area: rows = ' + textareaRow;
          } else if (editor === 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor') {
            dataRow.type.editorSelect.options = selectOptions;
            tdPropertyType += '<br>   - Select : options: ' + selectOptions;
          }
        }
      }
      newTableRow += tdPropertyType + '</td>';
      newTableRow += tdLabel;
      validators = $('#propertyValidator').val();
      dataRow.validators = validators;
      tdValidators = '<td>' + validators + '</td>';
      newTableRow += tdValidators;
      defaultValue = $('#propertyDefaultValue').val();
      tdDefaultValue = '<td>' + defaultValue + '</td>';
      dataRow.defaultValue = defaultValue;
      newTableRow += tdDefaultValue;
      dataRowSumbit = '<input type="hidden" name="properties[]" value="' + JSON.stringify(dataRow) + '">';
      tdAction = '<td>';
      tdAction += '&nbsp; <span data-action="delete" class="action action-delete" data-index="' + numberOfRows + '"><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
      tdAction += dataRowSumbit + '</td>';
      newTableRow += tdAction + '</tr>';
      $('.table-properties tbody').append(newTableRow);
      return numberOfRows++;
    } else {
      return alert('- Name and Label are required');
    }
  });

  $('#clearProperty').click(function() {
    $('#propertyName').val('');
    $('#propertyLabel').val('');
    $('#propertyValidator').val('');
    $('#propertyDefaultValue').val('');
    $('#propertyType').prop('selectedIndex', 0);
    $('.inline-editable-container').addClass('hide');
    $('#propertyIsInline').prop('checked', true);
    $('#propertyIsInlinePlaceholder').val('');
    $('#propertyIsInlinePlaceholder').removeClass('hide');
    $('#propertyEditors').addClass('hide');
    $('#propertyEditors').prop('selectedIndex', 0);
    return $('.editor-container').find('.editor').each(function() {
      $(this).addClass('hide');
      return $(this).val('');
    });
  });

  $('.table-properties tbody').on('click', 'span', function(e) {
    var $self;
    $self = $(this);
    if ($self.data('action') === 'delete') {
      if (confirm('Are you sure to delete this selected property?')) {
        return $('table.table-properties tr#' + $self.data('index')).remove();
      }
    }
  });

}).call(this);
