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
      return $('#propertyEditorText').addClass('hide');
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
      $('#propertyEditorTextAreaRow').removeClass('hide');
      return $('#propertyEditorTextAreaColumn').removeClass('hide');
    } else if ($self.val() === 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor') {
      return $('#propertyEditorSelect').removeClass('hide');
    }
  });

  numberOfRows = 1;

  $('#newProperty').click(function() {
    var defaultValue, editor, inlineEditable, inlineEditablePlaceholder, label, name, newTableRow, propertyType, selectOptions, tdAction, tdDefaultValue, tdLabel, tdName, tdPropertyType, tdValidators, textPlaceholder, textareaColumn, textareaRow, validators;
    name = $('#propertyName').val();
    label = $('#propertyLabel').val();
    if (name !== '' && label !== '') {
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
      textareaColumn = $('#propertyEditorTextAreaColumn').val();
      selectOptions = $('#propertyEditorSelect').val();
      if (propertyType === 'string') {
        propertyType += ':';
        if (inlineEditable.is(':checked')) {
          tdPropertyType += '<br>   - Inline editable: ' + inlineEditablePlaceholder;
        } else {
          if (editor === 'default') {
            tdPropertyType += '<br>   - Default text: ' + textPlaceholder;
          } else if (editor === 'TYPO3.Neos/Inspector/Editors/TextAreaEditor') {
            tdPropertyType += '<br> Text area: rows = ' + textareaRow + ', and cols = ' + textareaColumn;
          } else if (editor === 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor') {
            tdPropertyType += '<br>   - Select : options: ' + selectOptions;
          }
        }
      }
      newTableRow += tdPropertyType + '</td>';
      newTableRow += tdLabel;
      validators = $('#propertyValidator').val();
      tdValidators = '<td>' + validators + '</td>';
      newTableRow += tdValidators;
      defaultValue = $('#propertyDefaultValue').val();
      tdDefaultValue = '<td>' + defaultValue + '</td>';
      newTableRow += tdDefaultValue;
      tdAction = '<td>';
      tdAction += '&nbsp; <span data-action="delete" class="action action-delete" data-index="' + numberOfRows + '"><i class="fa fa-trash-o" aria-hidden="true"></i></span>';
      tdAction += '</td>';
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
