(function()
{
var pluginName = 'signature';
// ������������ ��� ������� .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//��������� ������� �� ������� ������
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[signature]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// ��������� ��������
editor.ui.addButton( 'Signature',
{
label : 'Add signature',//Title ������
command : pluginName,
icon : this.path + 'signature.png'//���� � ������
});
}
});
})();