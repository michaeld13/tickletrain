(function()
{
var pluginName = 'firstname';
// ������������ ��� ������� .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//��������� ������� �� ������� ������
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[firstname]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// ��������� ��������
editor.ui.addButton( 'Firstname',
{
label : 'Add firstname',//Title ������
command : pluginName,
icon : this.path + 'fname.png'//���� � ������
});
}
});
})();