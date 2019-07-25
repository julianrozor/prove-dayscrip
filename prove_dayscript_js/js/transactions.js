$(document).ready(function(){
  var header = [];

  $.getJSON( "http://s3-sa-east-1.amazonaws.com/dayscript-developer-test/users.json", function( data ) {
    var items = [];
    $.each( data, function( key, user ) {
        //read Headers
        if(key == 0){
          //load hedaders
          header = Object.keys(user);
        }
        //Created container tr
      $('#container-data').append('<tr class="item-'+key+'" ></tr>');
      //Get items for tr
      $.each( user, function( keyUid, valUid ) {
        //Validate Key User Id is diferent to id and username
        if(keyUid != 'id' && keyUid != 'username'){
          $('.item-'+key).append('<th class="'+keyUid.replace('_','-')+"-"+valUid+'">'+valUid+'</th>');  
        } 
      });
      //Adding item delete
      $('.item-'+key).append('<th><a class="item-delete-'+key+'" href="#">Delete</a></th>');
      //Create Event Click for item delete
      $('.item-delete-'+key).on('click',function(){
        $('.item-'+key).remove();
      });
    });
    //Get items Header
    $.each(header,function(hkey, hval){
      //Validate Key  is diferent to id, username and company_id
      if(hval != 'id' && hval != 'username' && hval != 'company_id'){
        $('.header-data').append('<th>'+hval+'</th>');
      } 
      //Validate Keyis equal to company_id
      else if(hval == 'company_id'){
        //change name for header company_id to company
        $('.header-data').append('<th>'+hval.replace('_id','')+'</th>');
      }
    });
    //Adding last item Header data
    $('.header-data').append('<th>Action</th>');
    //Get items get companies
    $.getJSON( "http://s3-sa-east-1.amazonaws.com/dayscript-developer-test/companies.json", function( data ) {
      $.each( data, function( key, company ) {
        //Set names for company Id
        $('.company-id-'+company.id).html(company.name);
      });
    });
  });
  // used for filter usin filter from jQuery
  $("#search-input").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#container-data tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});