(function()
{
var pluginName = 'lastname';
// ������������ ��� ������� .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//��������� ������� �� ������� ������
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[lastname]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// ��������� ��������
editor.ui.addButton( 'Lastname',
{
label : 'Add lastname',//Title ������
command : pluginName,
icon : this.path + 'lname.png'//���� � ������
});
}
});
})();