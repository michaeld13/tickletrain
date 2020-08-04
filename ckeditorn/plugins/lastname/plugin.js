(function()
{
var pluginName = 'lastname';
// Регистрируем имя плагина .
CKEDITOR.plugins.add( pluginName,
{
init : function( editor )
{//Добавляем команду на нажатие кнопки
 var cmd = new CKEDITOR.command( editor,
    {
        exec : function( editor )
        {
            editor.insertHtml('[lastname]');
			//alert( editor.document.getBody().getHtml() );
        }
    });
editor.addCommand( pluginName,cmd);
// Добавляем кнопочку
editor.ui.addButton( 'Lastname',
{
label : 'Add lastname',//Title кнопки
command : pluginName,
icon : this.path + 'lname.png'//Путь к иконке
});
}
});
})();