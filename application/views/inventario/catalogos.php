
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
<div class="dataTables_filter" id="dyntable_filter">
	<label>
		Search: <input type="text" aria-controls="dyntable">
	</label>
</div>
{tabla}
</div>
