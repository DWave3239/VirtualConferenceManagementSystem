/* HELPER FUNCTIONS */
function getParent(elem, parentTag = null, parentClass = null){
    if(parentTag && parentClass){
        while(elem.tagName != parentTag && !elem.classList.contains(parentClass)){
            elem = elem.parentElement;
        } 
    }else if(parentTag){
        while(elem.tagName != parentTag){ 
            elem = elem.parentElement;
        }
    }else if(parentClass){
        while(!elem.classList.contains(parentClass)){ 
            elem = elem.parentElement;
        }
    }
    return elem;
}

function removeClassFromSet(set, className, func = null){
    while(set.length > 0){
        if(func instanceof Function){
            func(set[0]);
        }
        set[0].classList.remove(className);
    }
}

function addClassToSet(set, className, func = null){
    for(let i=0; i<set.length; i++){
        if(func instanceof Function){
            func(set[i]);
        }
        set[i].classList.add(className);
    }
}

function openNextMore(source){
    let tr = getParent(source, "TR");
    removeClassFromSet(tr.getElementsByClassName('hide'), 'hide');
    addClassToSet(tr.getElementsByClassName('show-more'), 'hide');
}

function showLess(source){
    let tr = getParent(source, "TR");
    removeClassFromSet(tr.getElementsByClassName('hide'), 'hide');
    addClassToSet(tr.getElementsByClassName('show-less'), 'hide');
    addClassToSet(tr.getElementsByClassName('more'), 'hide');
}

function padToLength(num, width){
    num = num + "";
    return "0".repeat(Math.max(width - num.length, 0)) + num;
}

function openModal(elem){
    let target_modal = document.getElementsByClassName(elem.dataset.modal)[0];
    console.log(target_modal);
}

function toggleDayTable(elem){
    let table = getParent(elem, "DIV").getElementsByTagName("table")[0];
    if(table.style.display == '' || table.style.display == 'block'){
        table.style.display = 'none';
    }else{
        table.style.removeProperty('display');
    }
}