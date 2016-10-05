##################
# Jquery plugins #
##################

# Restrict input only a-z or A-Z
$.fn.onlyCharacter = ->
	this.keypress((e)->
		if not /[0-9a-zA-Z-]/.test(String.fromCharCode(e.which))
			return false
	)

# Restrict input only number 0-9
$.fn.onlyNumber = ->
	this.keypress((e)->
		if not /[0-9]/.test(String.fromCharCode(e.which))
			return false
	)


# Declare class to restrict only character
$('.only-character').onlyCharacter()

# Declare class to restrict only number
$('.only-number').onlyNumber()

# Active bootstrap tab
$('#tabNodetype a').click((e)->
	e.preventDefault()
	$(this).tab('show')
)

# For enable button reload the page
$('.reload-page').click(->
	window.location.reload()
)



########################
# NodeType information #
########################

# Helper checkbox
$('#helper').click(->
	if $(this).is(':checked')
		$('#helperMessage').removeClass('hide')
	else
		$('#helperMessage').addClass('hide')
)

# Create new group checkbox
$('#createGroupAtInspector').click(->
	if $(this).is(':checked')
		$('#groupName').removeClass('hide')
	else
		$('#groupName').addClass('hide')
)



#######################
# NodeType properties #
#######################

# Type select change
$('#propertyType').change(->
	$self = $(this)
	if $self.val() is 'string'
		$('.inline-editable-container').removeClass('hide')
	else
		$('.inline-editable-container').addClass('hide')
)

# Is inline-editable checkbox
$('#propertyIsInline').click(->
	if $(this).is(':checked')
		$('#propertyIsInlinePlaceholder').removeClass('hide')
		$('#propertyEditors').addClass('hide')
		$('#propertyEditorText').addClass('hide')
	else
		$('#propertyIsInlinePlaceholder').addClass('hide')
		$('#propertyEditorText').removeClass('hide')
		$('#propertyEditors').removeClass('hide')
)

# Editor select change
$('#propertyEditors').change(->
	$self = $(this)

	# Hide all editor
	$('.editor-container').find('.editor').each(->
		$(this).addClass('hide')
	)

	if $self.val() is 'default'
		$('#propertyEditorText').removeClass('hide')
	else if $self.val() is 'TYPO3.Neos/Inspector/Editors/TextAreaEditor'
		$('#propertyEditorTextAreaRow').removeClass('hide')
		$('#propertyEditorTextAreaColumn').removeClass('hide')
	else if $self.val() is 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor'
		$('#propertyEditorSelect').removeClass('hide')
)


numberOfRows = 1
# Button add new property
$('#newProperty').click(->
	name = $('#propertyName').val()
	label = $('#propertyLabel').val()
	if name isnt '' and label isnt ''
		newTableRow = '<tr id="' + numberOfRows + '">'
		newTableRow += '<td>' + numberOfRows + '</td>'
		tdName = '<td>' + name + '</td>'
		tdLabel = '<td>' + label + '</td>'
		newTableRow += tdName

		propertyType = $('#propertyType').val()
		tdPropertyType = '<td>' + propertyType

		inlineEditable = $('#propertyIsInline')
		inlineEditablePlaceholder = $('#propertyIsInlinePlaceholder').val()

		editor = $('#propertyEditors').val()
		textPlaceholder = $('#propertyEditorText').val()
		textareaRow = $('#propertyEditorTextAreaRow').val()
		textareaColumn = $('#propertyEditorTextAreaColumn').val()
		selectOptions = $('#propertyEditorSelect').val()

		if propertyType is 'string'
			propertyType += ':'
			if inlineEditable.is(':checked')
				tdPropertyType += '<br>   - Inline editable: ' + inlineEditablePlaceholder
			else
				if editor is 'default'
					tdPropertyType += '<br>   - Default text: ' + textPlaceholder
				else if editor is 'TYPO3.Neos/Inspector/Editors/TextAreaEditor'
					tdPropertyType += '<br> Text area: rows = ' + textareaRow + ', and cols = ' + textareaColumn
				else if editor is 'TYPO3.Neos/Inspector/Editors/SelectBoxEditor'
					tdPropertyType += '<br>   - Select : options: ' + selectOptions

		newTableRow += tdPropertyType + '</td>'

		newTableRow += tdLabel

		validators = $('#propertyValidator').val()
		tdValidators = '<td>' + validators + '</td>'
		newTableRow += tdValidators

		defaultValue = $('#propertyDefaultValue').val()
		tdDefaultValue = '<td>' + defaultValue + '</td>'
		newTableRow += tdDefaultValue

		tdAction = '<td>'
		tdAction += '&nbsp; <span data-action="delete" class="action action-delete" data-index="' + numberOfRows + '"><i class="fa fa-trash-o" aria-hidden="true"></i></span>'
		tdAction += '</td>'

		newTableRow += tdAction + '</tr>'
		$('.table-properties tbody').append(newTableRow)
		numberOfRows++
	else
		alert('- Name and Label are required')
)

# Clear input property information
$('#clearProperty').click(->
	$('#propertyName').val('')
	$('#propertyLabel').val('')
	$('#propertyValidator').val('')
	$('#propertyDefaultValue').val('')

	$('#propertyType').prop('selectedIndex', 0)
	$('.inline-editable-container').addClass('hide')
	$('#propertyIsInline').prop('checked', true)

	$('#propertyIsInlinePlaceholder').val('')
	$('#propertyIsInlinePlaceholder').removeClass('hide')

	$('#propertyEditors').addClass('hide')
	$('#propertyEditors').prop('selectedIndex', 0)

	# Hide and clear all editor
	$('.editor-container').find('.editor').each(->
		$(this).addClass('hide')
		$(this).val('')
	)
)

# Delete specific row of table properties
$('.table-properties tbody').on('click', 'span', (e)->
	$self = $(this)
	if $self.data('action') is 'delete'
		if confirm('Are you sure to delete this selected property?')
			$('table.table-properties tr#' + $self.data('index')).remove()
)
