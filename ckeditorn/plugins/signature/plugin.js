(function()
{
var pluginName = 'signature';
// Регистрируем имя плагина .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//Добавляем команду на нажатие кнопки
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[signature]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// Добавляем кнопочку
editor.ui.addButton( 'Signature',
{
label : 'Add signature',//Title кнопки
command : pluginName,
icon : this.path + 'signature.png'//Путь к иконке
});
}
});
})();