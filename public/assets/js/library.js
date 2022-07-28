    //регистрируем нажатие
    function treeHide() {
    let tar = event.target.tagName;
    //если место нажатия не имя то ничего
    if(tar != 'SPAN') return;

    let childrenCont = event.target.parentNode.querySelector('ol');
    if (!childrenCont) return;
    //скрытие или открытие при нажатии
    childrenCont.hidden = !childrenCont.hidden;

    //меняем класс для отображения -+
    if(!event.target.classList.contains('show') && !event.target.classList.contains('hide')) return;
    if(event.target.classList.contains('show')){
        event.target.classList.add('hide');
        event.target.classList.remove('show');
    }
    else{
        event.target.classList.add('show');
        event.target.classList.remove('hide');
    }
}


