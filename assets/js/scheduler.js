class Scheduler2 {

    constructor(sid, enableHighlightUpdates=true) {
        this.offset = new Date().getTimezoneOffset();
        this.sid = sid; //scheduleId
        this.tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
        this.schedule = document.getElementById(sid);

        this.rewriteTimes(true);

        let scheduler = this;

        if(enableHighlightUpdates) { setInterval(function() { if ( new Date().getSeconds() === 0 ) scheduler.doHighlights(); }, 1000); }
    }

    // converts dates and times in the schedule into correct timezone
    convertToTimezone(){
        let days = this.schedule.getElementsByClassName("day");
        for(let i=0; i<days.length; i++){
            let day = days[i].dataset.date.split('.');
            days[i].getElementsByClassName("day_index")[0].innerHTML = i+1;
            
            /* convert H:i:s to timestamp */
            let times = days[i].getElementsByClassName("time_from");
            for(let j=0; j<times.length; j++){
                let parent = getParent(times[j], "TR");
                let time = parent.dataset.timefrom.split(':');
                let date = new Date(day[2], day[1]-1, day[0], parseInt(time[0]) - parseInt(this.offset/60), parseInt(time[1]) - parseInt(this.offset%60));
                times[j].innerHTML = padToLength(date.getHours(), 2)+':'+padToLength(date.getMinutes(), 2);
                parent.dataset.timestampfrom = date.getTime();
                if(j == 0){
                    date.setHours(0, 0, 0);
                    days[i].getElementsByClassName("day_date")[0].innerHTML = date.getDate()+'.'+padToLength(date.getMonth()+1, 2)+'.'+date.getFullYear();
                    days[i].dataset.timestamp = date.getTime();
                }
            }

            /* convert H:i:s to timestamp */
            times = days[i].getElementsByClassName("time_until");
            for(let j=0; j<times.length; j++){
                let parent = getParent(times[j], "TR");
                let time = parent.dataset.timeuntil.split(':');
                let date = new Date(day[2], day[1]-1, day[0], parseInt(time[0]) - parseInt(this.offset/60), parseInt(time[1]) - parseInt(this.offset%60));
                times[j].innerHTML = padToLength(date.getHours(), 2)+':'+padToLength(date.getMinutes(), 2);
                getParent(times[j], "TR").dataset.timestampuntil = date.getTime();
            }

            let delays = days[i].querySelectorAll('td[data-delay]');
            for(let j=0; j<delays.length; j++){
                let time = delays[j].dataset.delay.split(':');
                let sign = (parseInt(time[0]) >= 0) ? 1 : -1;
                if(sign){
                    time[0] = -time[0];
                }
                delays[j].dataset.delaytimestamp = sign*((((time[0]*24)+time[1])*60+time[2])*1000);
            }
        }
    }

    // this function colors the schedule depending on the 
    doHighlights(){
        let days = this.schedule.getElementsByClassName("day");

        let today = new Date();
        let tomorrow = new Date(today);
        tomorrow.setDate(today.getDate()+1);

        let now = Date.now();
        for(let i=0; i<days.length; i++){
            if(days[i].dataset.timestamp >= tomorrow.setHours(0, 0, 0, 0)){ // day is ahead of time
                continue;
            }

            // day in progress
            days[i].classList.add('in_progress');
            let times = days[i].getElementsByClassName("time_until");
            let all_passed = true;
            for(let j=0; j<times.length; j++){
                const time_until = getParent(times[j], "TR");
                
                // include rowspan
                const td = time_until.getElementsByTagName("TD")[0];
                let rowsToCover = td.getAttribute('rowspan') ?? 1;
                
                if(time_until.dataset.timestampuntil < now){ // part has already passed
                    let target = time_until;
                    while(rowsToCover > 0){
                        target.classList.add('table-success');
                        removeClassFromSet(target.getElementsByClassName("in_progress"), "in_progress", function(e){e.classList.add("table-success"); e.style.removeProperty("background");});
                        removeClassFromSet(target.getElementsByClassName("btn-primary"), "btn-primary", function(e){e.classList.add("btn-light");})
                        target = target.nextElementSibling;
                        rowsToCover--;
                    }
                }else{ // part is not over yet
                    all_passed = false;
                    const time_from = time_until;
                    let row = time_from;
                    if(time_from.dataset.timestampfrom <= now){ // part has started
                        const time_span = Math.round((time_until.dataset.timestampuntil - time_from.dataset.timestampfrom)/60);
                        const time_passed = Math.round((now - time_from.dataset.timestampfrom)/60);
                        const maxRowRatio = Math.round(100/rowsToCover);
                        let ratio = Math.round((time_passed / time_span)*100); // ratio of rowspanned cell
                        let first = true;
                        for(let k=0; k<rowsToCover;k++){
                            let target = row.getElementsByTagName("TD")[0]; // inverse of getParent
                            let rowRatio = Math.max(0, Math.min(ratio, maxRowRatio*(k+1))); // counter negative values
                            while(target){
                                if(first){ // rowspanned cell
                                    target.style.background = "linear-gradient(#c3e6cb, #c3e6cb "+(ratio)+"%, white "+(ratio)+"%, white)";
                                    first = false;
                                }else{ // non-rowspan cells
                                    rowRatio *= rowsToCover;
                                    target.style.background = "linear-gradient(#c3e6cb, #c3e6cb "+(rowRatio)+"%, white "+(rowRatio)+"%, white)";
                                }
                                target.classList.add("in_progress");
                                removeClassFromSet(target.getElementsByClassName("btn-light"), "btn-light", function(e){e.classList.add("btn-primary");})
                                target = target.nextElementSibling;
                            }
                            row = row.nextElementSibling;
                            ratio -= maxRowRatio;
                        }
                    }else{ // part has not yet started
                        while(rowsToCover > 0){
                            let target = row.getElementsByTagName('TD')[0]; // inverse of getParent
                            while(target){
                                removeClassFromSet(target.getElementsByClassName("table-success"), "table-success");
                                removeClassFromSet(target.getElementsByClassName("in_progress"), "in_progress", function(e){e.style.removeProperty("background");});
                                target = target.nextElementSibling;
                            }

                            target = time_from;
                            target.classList.remove("table-success");
                            removeClassFromSet(target.getElementsByClassName("in_progress"), "in_progress", function(e){e.style.removeProperty("background");});
                        
                            row = row.nextElementSibling;
                            rowsToCover--;
                        }
                    }
                }
            }
            if(all_passed){
                days[i].classList.remove('in_progress');
                days[i].classList.add('past');
            }
        }
    }

    rewriteTimes(init = false, obj=null){
        if(obj){
            let timezone_offset = obj.schedule.getElementsByClassName("timezone-offset")[0];
            obj.offset = - timezone_offset.value;
            obj.convertToTimezone();
            obj.doHighlights();

            this.updateServerTimezone(obj.offset);
        }else{
            let timezone_offset = this.schedule.getElementsByClassName("timezone-offset")[0];
            if(init){
                timezone_offset.value = -this.offset;
                timezone_offset.addEventListener("select", () => this.rewriteTimes(false, this));
                timezone_offset.addEventListener("change", () => this.rewriteTimes(false, this));
            }else{
                this.offset = - timezone_offset.value;
            }
            this.convertToTimezone();
            this.doHighlights();
            
            this.updateServerTimezone(this.offset);
        }
    }

    fakeSchedule(){
        var temp = this.schedule.getElementsByClassName("template")[0];
    
        const today = new Date();
    
        for(let i = -1; i < 2; i++){
            let date = new Date(today);
            let clone = temp.cloneNode(true);
            date.setDate(date.getDate() + i);
            clone.dataset.date = date.getDate()+'.'+padToLength(date.getMonth()+1, 2)+'.'+date.getFullYear();
            clone.classList.remove('template');
            temp.parentElement.appendChild(clone);
        }
    }

    updateServerTimezone(offset_in_minutes){
        const base_href = document.getElementsByTagName('base')[0].href;
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() { }
        xhttp.open("GET", base_href+"index.php?main/setTimezoneOffset/"+offset_in_minutes, true);
        xhttp.send();
    }
}