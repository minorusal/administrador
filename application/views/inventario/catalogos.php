
<!--pageheader-->
<div class="pageheader">
    <div class="pageicon"><span class="iconfa-book"></span></div>
    <div class="pagetitle">
        <h1>{titulo}</h1>
	</div>
</div>
<!--pageheader-->
	
<!--maincontent-->
<div class="maincontent">
<div class="maincontentinner">
	<!--row-fluid-->
    <div class="row-fluid">
<h4 class="widgettitle">{widgettitle}</h4>
<div id="dyntable_wrapper" class="dataTables_wrapper" role="grid">
<div id="dyntable_length" class="dataTables_length">
    <a href="" class="btn btn-primary btn-rounded"> 
      	<i class=" iconfa-edit"></i>
      	&nbsp; Agregar Articulo
    </a>
</div>
<div class="dataTables_filter">
	<label>
		Buscar: <input type="text" aria-controls="dyntable">
	</label>
</div>
{tabla}

<div class="dataTables_info" id="dyntable_info">
	{item_info}
</div>
<div class="dataTables_paginate paging_full_numbers" id="dyntable_paginate">
	<a tabindex="0" class="first paginate_button paginate_button_disabled" id="dyntable_first">First</a>
	<a tabindex="0" class="previous paginate_button paginate_button_disabled" id="dyntable_previous">Previous</a>
	<span>
		<a tabindex="0" class="paginate_active">1</a>
			<a tabindex="0" class="paginate_button">2</a>
	</span>
	<a tabindex="0" class="next paginate_button" id="dyntable_next">Next</a><a tabindex="0" class="last paginate_button" id="dyntable_last">Last</a>
</div>
</div>

