$('#tabNodetype a').click((e)->
	e.preventDefault()
	$(this).tab('show')
)

$('.reload-page').click(->
	window.location.reload()
)
