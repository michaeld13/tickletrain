(function()
{
var pluginName = 'firstname';
// Регистрируем имя плагина .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//Добавляем команду на нажатие кнопки
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[firstname]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// Добавляем кнопочку
editor.ui.addButton( 'Firstname',
{
label : 'Add firstname',//Title кнопки
command : pluginName,
icon : this.path + 'fname.png'//Путь к иконке
});
}
});
})();