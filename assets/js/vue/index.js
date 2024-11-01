    var iframeSource = vue_js_object.baseUrl+'api/iframe';
    var iframe = document.createElement('iframe');
    iframe.setAttribute('src', iframeSource);
    iframe.setAttribute('id', 'the_iframe');
    iframe.style.width = 1 + 'px';
    iframe.style.height = 1 + 'px';
    document.body.appendChild(iframe);
    var iframeEl = document.getElementById('the_iframe');
    
    // Send a message to the child iframe
    var sendMessage = function(msg) {     
        // Make sure you are sending a string, and to stringify JSON.
        setTimeout(function() {
         iframeEl.contentWindow.postMessage(msg, '*');
        }, 2000);
    };
    var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9+/=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/rn/g,"n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}

    var broHistory = function(name,type){
        var uri = window.location.toString();
        var defaults = {tab:type,url:window.location.href}
        if (uri.indexOf("#") > 0) {
         var url = uri.substring(0, uri.indexOf("#"));
         defaults = {url:url};//tab:name[1],
        }
        defaults.url += '#'+name+':'+type;
        window.history.replaceState({}, name, defaults.url);
    }
    /**
     * Common Component
     * 
     */
    /** Select2 Js Component ***/
    Vue.component('select2', {
        template: '#select2-template',
        props: ['options', 'value', 'name','cls','placeholder','type','tag','except'],
        data(){
            return{
              sObject:null
            }
        },
        mounted: function () {
            var vm = this
            vm.sObject = jQuery(this.$el).select2({ data: this.options ,
                              placeholder: vm.placeholder,
                              tags: vm.tag,
                              tokenSeparators: [','],
                              createTag: function (params) {
                                var value = params.term;
                                if(vm.tag){
                                  if(vm.$options.filters.validateEmail(value) && $.inArray(value,vm.except)=='-1'){
                                    return {
                                      id: params.term,
                                      text: params.term,
                                      newOption: true
                                    }  
                                  }
                                }else{
                                  return {
                                      id: params.term,
                                      text: params.term,
                                      newOption: true
                                    }
                                }
                                
                              },/*templateResult: function (data) {
                                var $result = jQuery("<span></span>");

                                $result.text(data.text);

                                if (data.newOption) {
                                  $result.append(" <em>(new)</em>");
                                }

                                return $result;
                              },*/
                              escapeMarkup: function (m) {
                                return m;
                              },
                              templateResult: function (option) {
                                var originalOption = option.element;
                                if (!option.id) { return option.text; }

                                var $result = jQuery("<span></span>");

                                $result.text(option.text);

                                if (option.newOption) {
                                  return $result.append(" <em>(new)</em>");
                                }else if(option.img){
                                  return '<img src="'+option.img+'" /> '+ option.text ;  // replace image source with option.img (available in JSON)
                                }else if(jQuery(option.element).data('img')){
                                  return '<img src="'+$(option.element).data('img')+'" /> '+ option.text ;  // replace image source with option.img (available in JSON)
                                }
                                return $result;
                              },
                              templateSelection: function(option) {
                                if(option.img){
                                  return '<img src="'+option.img+'" /> '+ option.text ;  // replace image source with option.img (available in JSON)
                                }else if(jQuery(option.element).data('img')){
                                  return '<img src="'+$(option.element).data('img')+'"/> '+option.text;
                                }
                                return option.text;
                              }
                          })
                          .val(this.value)
                          .trigger('change')
                          // emit event on change.
                          .on('change', function () {
                            vm.$emit('input', jQuery(this).val(),vm.sObject);
                            //vm.$emit('input', this.value)
                          });
        },
        watch: {
        value: function (value) {
          // update value
          if ([...value].sort().join(",") !== [...jQuery(this.$el).val()].sort().join(","))
            jQuery(this.$el).val(value).trigger('change');
        },
        options: function (options) {
          // update options
          //options
          jQuery(this.$el).empty().select2({ data: options ,placeholder: this.placeholder,})
        }
        },
        destroyed: function () {
            jQuery(this.$el).off().select2('destroy')
        }
    });
    /** Model Popup ***/
    Vue.component('model-popup', {
        template: '#model-popup-template',
        props: [],
        directives: {
            'autofocus': {
                inserted(el) {
                    el.focus();
                }
            }
        },
        data: function() {
            return {
            }
        }
    });
    /** jKanban Component Board ***/
    Vue.component('kanban-board', {
        template: '#kanban-template',
        props: ['boards'],
        data: function() {
            return {
                tasks:{},
                currency:'',
                pageTitle:"Task board",
                options:[],
                showModal:false,
                itemIndex:''
            }
        },
        created(){
            //alert("asdasd");
        },
        beforeMount() {
            //alert("beformount")
        },
        mounted () {
            //this.bindEvent(window, 'message',this.getResponse);
            var app = this;
            var KanbanComponent = new jKanban({
                    element: '#kanban-board',
                    gutter: '10px',
                    widthBoard: '330px',
                    buttonClick: function (el, boardId) {// callback when the board's button is clicked
                        console.log(el);
                        console.log(boardId);
                        // create a form to enter element 
                        var formItem = document.createElement('form');
                        formItem.setAttribute("class", "itemform");
                        formItem.innerHTML = '<div class="form-group"><textarea class="form-control" rows="2" autofocus></textarea></div><div class="form-group"><button type="submit" class="btn btn-primary btn-xs pull-right">Submit</button><button type="button" id="CancelBtn" class="btn btn-default btn-xs pull-right">Cancel</button></div>'

                        KanbanComponent.addForm(boardId, formItem);
                        formItem.addEventListener("submit", function (e) {
                            e.preventDefault();
                            var text = e.target[0].value
                            KanbanComponent.addElement(boardId, {
                                "title": text,
                            })
                            formItem.parentNode.removeChild(formItem);
                        });
                        document.getElementById('CancelBtn').onclick = function () {
                            formItem.parentNode.removeChild(formItem)
                        }
                    },
                    addItemButton : false,
                    dragBoards : false, 
                    boards: app.boards,
                    click           : function (el) {// callback when any board's item are clicked
                                        //console.log(el.dataset);
                                        var iId = el.dataset.eid;
                                        if(el.dataset.action && el.dataset.action=='paypal-pay'){
                                            //alert("paypal button");
                                            app.$emit('paypal',el);
                                        }else if(el.dataset.close){
                                            app.$emit('delete_item',el);
                                            KanbanComponent.removeElement(iId);
                                        }else{
                                            app.$emit('detail_page',el);
                                        }
                                        //
                                        //alert(el.innerHTML);
                                    },                             
                    dragEl          : function (el, source) {// callback when any board's item are dragged
                                        //Disabled done board in case of in-progress task which comes under agency, move on done board 
                                        if(source.parentNode.dataset.id=='_in_progress'){
                                            if(el.dataset.progressmove=='0'){
                                                KanbanComponent.findBoard('_done').classList.add('disabled-board');
                                            }
                                        }else if(source.parentNode.dataset.id=='_draft'){
                                            if(el.dataset.progressmove=='0'){
                                                KanbanComponent.findBoard('_in_progress').classList.add('disabled-board');
                                            }else if(el.dataset.progressmove=='1'){
                                                KanbanComponent.findBoard('_estimation').classList.add('disabled-board');
                                            }
                                        }

                                    },
                    dragendEl       : function (el) {// callback when any board's item stop drag
                                        //alert("dragend");
                                    },
                    dropEl          : function (el, target, source, sibling) {// callback when any board's item drop in a board
                                        //Check board allow to move on target board or not
                                        var move = true;
                                        var dragTo =  source.parentNode.dataset.dragto.split(',');
                                        if(dragTo.indexOf(target.parentNode.dataset.id) === -1 && target.parentNode.dataset.id !== source.parentNode.dataset.id){
                                            move = false;
                                        }
                                        if(move && target.parentNode.dataset.id!=source.parentNode.dataset.id){
                                            //check in-progress task valid for move on done board
                                            if(source.parentNode.dataset.id=='_in_progress'){
                                                if(el.dataset.progressmove=='0'){
                                                    move = false;
                                                    KanbanComponent.drake.cancel(true);//move drag item back to source board
                                                }
                                            }else if(source.parentNode.dataset.id=='_draft'){
                                                if(el.dataset.progressmove=='0' && target.parentNode.dataset.id=='_in_progress'){
                                                    move = false;
                                                    KanbanComponent.drake.cancel(true);
                                                }else if(el.dataset.progressmove=='1' && target.parentNode.dataset.id=='_estimation'){
                                                    move = false;
                                                    KanbanComponent.drake.cancel(true);
                                                }
                                            }
                                            //update task status when task moved on target board properly
                                            if(move){
                                                app.updateStatus(el.dataset.eid,target.parentNode.dataset.id);
                                            }
                                        }                                        
                                    },    
                    dragBoard       : function (el, source) {// callback when any board stop drag
                                    },
                    dragendBoard    : function (el) {// callback when any board stop drag
                                    },
                });            
        },
        methods: {
            updateStatus(id,tId){
                this.$emit('rebuild',id,tId);
            }
        }
    });


    /**
     * 
     * Component Page 
     *
     */
    Vue.component('task-boards', {
        template: '#boards-template',
        props: ['error','config','currentuser'],
        //template:require('./template/Listing.html'),
        data: function() {
            return {
                data: '0',
                tvar:"test wordpress",
                tasks: [],
                options:[],
                defaultTeam:this.config.defaultTeam,
                taskStatus: {},
                img:{'checkbox':'images/check.png'},
                defaultStatus: '',
                pageTitle:"All tasks",
                estimationRes:false,
                env:"sandbox",
                currency_code:"USD",
                currency:"$",
                paypal: {},
                experienceOptions: {
                    input_fields: {
                      no_shipping: 1
                    }
                  },
                aStyle: {
                    label: 'pay',
                    size: 'small',
                    shape: 'rect',
                    color: 'blue',
                  },
                interval: null,
                callNum:3,
                showModal:false,
                showPayModal:false,
                itemIndex:'',
                priorities:[],
                priority:'',
                walletBalance:0,
                boards:[],
                totalItem:0,
            }
        },
        computed:{
          isApp:function(){
            return false;
          },
          isAdmin:function(){
            return false;
          },
        },
        mounted () {
            //console.log(this.$options.filters.formatDate('2018-10-25 07:23:22')+" gopal teri jai");
            var uri = window.location.toString();
            let type = 'all';
            if (uri.indexOf("#") > 0) {
                var name = uri.substring(uri.indexOf("#boards:")).split('#boards:');
                if(name[1])
                    type = name[1];
            }else{
                broHistory("boards","all");
            }
            
            Task.showLoader();
            setTimeout(function () {
                this.getTaskBoard();
            }.bind(this), 3000); 
            this.bindEvent(window, 'message',this.getResponse);
        },
        methods: {
            callFun(){
                alert("asdasd call wordpress function ");
            },
            clearInterVal(){
                //console.log("clear interval");
                clearInterval(this.interval);
            },
            getPaypalItem(item){
                return [{"name": item.name,
                        "description": item.description,
                        "sku": Base64.encode(item.md5),
                        "quantity": "1",
                        "price": this.$options.filters.priceFormat(item.price),
                        "currency": this.currency_code}];
            },
            isClient(){
                return true;
            },
            async bindEvent(element, eventName, eventHandler) {
                if (element.addEventListener){
                    element.addEventListener(eventName, eventHandler, false);
                } else if (element.attachEvent) {
                    element.attachEvent('on' + eventName, eventHandler);
                }
            },
            getResponse(e){
                let taskList = jQuery.parseJSON(e.data);
                if(taskList.status){
                    this.showModal = false;
                    this.itemIndex = '';
                    this.defaultTeam = this.config.defaultTeam;
                    Task.hideLoader();
                }else if(taskList.boards){
                    this.totalItem = taskList.totalItem;
                    this.boards = taskList.boards;
                    this.tasks = taskList.task;
                    this.env = taskList.env;
                    this.paypal = taskList.paypal;
                    this.currency = taskList.currency.symbol;
                    this.currency_code = taskList.currency.code;
                    this.walletBalance = taskList.walletBalance;
                    Task.hideLoader();
                }else{
                    Task.hideLoader();
                }
            },
            /**/
            async sendMsg(msg) {
                setTimeout(function() {
                    sendMessage(msg);
                },1500);
            },
            getTaskBoard(){
                let params = {api:"task_boards",token:this.currentuser.admin_token,email:this.currentuser.admin_email};
                this.sendMsg(''+JSON.stringify(params));
            },
            kanban_click_handler(el){
                Task.showLoader();
                this.$emit('update', 'task-details');
                broHistory("task",el.dataset.eid);
            },
            kanban_get_task(id,status){
                //alert(this.totalItem);
                this.totalItem = 0;
                //this.boards = [];
                Task.showLoader();
                //this.getTaskBoard();
                this.sendMsg(''+JSON.stringify({api:"task_board_status",token:this.currentuser.admin_token,post:"status="+status+"&totalItem="+this.totalItem,tId:id,email:this.currentuser.admin_email}));
            },
            kanban_delete_item(el){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_delete",token:this.currentuser.admin_token,tId:el.dataset.eid,email:this.currentuser.admin_email}));
                //Task.hideLoader();             
            },
            kanban_callPayPopup(el){
                //console.log(el.dataset)
                Task.showLoader();
                this.itemIndex = el.dataset.index;
                this.showPayModal = true;
                Task.hideLoader();
            },
            paymentAuthorized: function (data) {
                Task.showLoader();
                //console.log("payment payment-authorized");
                //console.log(data);
            },
            paymentCompleted: function (data) {
                let tdata = Base64.decode(data.transactions[0].item_list.items[0].sku);
                //console.log(data);
                //console.log(JSON.stringify({api:"task_payment",post:"txn="+data.transactions[0].related_resources[0].sale.id+"&amount="+data.transactions[0].amount.total+"&payer_id="+data.payer.payer_info.payer_id+"&task_details="+tdata}));
                this.sendMsg(''+JSON.stringify({api:"task_payment",token:this.currentuser.admin_token,post:"txn="+data.transactions[0].related_resources[0].sale.id+"&amount="+data.transactions[0].amount.total+"&payer_id="+data.payer.payer_info.payer_id+"&task_details="+tdata}));
                let type = this.defaultStatus;
                this.showPayModal = false;
                this.totalItem = 0;
                this.getTaskBoard();
            },
            paymentCancelled: function (data) {
                Task.hideLoader();
            },
            payWithWallet(index){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_payment",token:this.currentuser.admin_token,post:"amount="+this.tasks[index].price+"&pay=wallet&task_details="+(this.tasks[index].md5)}));
                let type = this.defaultStatus;
                this.showPayModal = false;
                this.defaultStatus = "";
                this.callNum = 2;
                this.totalItem = 0;
                this.getTaskBoard();
            },
            viewChange(){
                Task.showLoader();
                this.$emit('update', 'task-listing');
                broHistory("status",'all');
            },
        }
    })
    Vue.component('task-listing', {
        template: '#listing-template',
        props: ['error','config','currentuser'],
        //template:require('./template/Listing.html'),
        data: function() {
            return {
                data: '0',
                tasks: [],
                options:[],
                defaultTeam:this.config.defaultTeam,
                taskStatus: {},
                img:{'checkbox':'images/check.png'},
                defaultStatus: '',
                pageTitle:"All tasks",
                estimationRes:false,
                env:"sandbox",
                currency_code:"USD",
                paypal: {},
                experienceOptions: {
                    input_fields: {
                      no_shipping: 1
                    }
                  },
                aStyle: {
                    label: 'pay',
                    size: 'small',
                    shape: 'rect',
                    color: 'blue',
                  },
                interval: null,
                callNum:3,
                showModal:false,
                showPayModal:false,
                itemIndex:'',
                priorities:[],
                priority:'',
                walletBalance:0,
                boards:[],
                totalItem:0,
            }
        },
        computed:{
          isAdmin: function(){
            return false;
          },
          isApp:function(){
            return false;
          }
        },
        mounted () {
            //console.log(this.$options.filters.formatDate('2018-10-25 07:23:22')+" gopal teri jai");
            var uri = window.location.toString();
            let type = 'all';
            if (uri.indexOf("#") > 0) {
                var name = uri.substring(uri.indexOf("#status:")).split('#status:');
                if(name[1])
                    type = name[1];
            }
            
            /*this.getTasks(type);
            this.interval = setInterval(function () {
                //console.log("caall function");
                this.getTasks(type);
            }.bind(this), 3000); */
            Task.showLoader();
            setTimeout(function () {
                this.getTasks(type);
                //this.getTaskBoard();
            }.bind(this), 3000); 
            this.bindEvent(window, 'message',this.getResponse);
        },
        methods: {
            clearInterVal(){
                //console.log("clear interval");
                clearInterval(this.interval);
            },
            taskPriorityIcon:function(index){
                let prior = this.priorities.length-this.tasks[index].priority-1;
                return this.priorities[prior].img;
            },
            taskPriorityName:function(index){
                let prior = this.priorities.length-this.tasks[index].priority-1;
                return this.priorities[prior].text;
            },
            getPaypalItem(item){
                return [{"name": item.name,
                        "description": item.description,
                        "sku": Base64.encode(item.md5),
                        "quantity": "1",
                        "price": this.$options.filters.priceFormat(item.price),
                        "currency": this.currency_code}];
            },
            isClient(){
                return true;
            },
            async bindEvent(element, eventName, eventHandler) {
                if (element.addEventListener){
                    element.addEventListener(eventName, eventHandler, false);
                } else if (element.attachEvent) {
                    element.attachEvent('on' + eventName, eventHandler);
                }
            },
            getResponse(e){
                let taskList = jQuery.parseJSON(e.data);
                if(taskList.data){
                    this.data = 1;
                    this.env = taskList.env;
                    this.paypal = taskList.paypal;
                    this.tasks = taskList.data;
                    this.taskStatus = taskList.aTaskStatus;
                    this.currency = taskList.currency.symbol;
                    this.currency_code = taskList.currency.code;
                    this.walletBalance = taskList.walletBalance;
                    if(this.priority==''){
                        this.priorities = [{id:'all',text:"All Prorities"}];
                        jQuery.merge(this.priorities,taskList.priorities);
                        this.priority = 'all';
                    }
                    if(this.config.assignAgency){
                        this.options = taskList.team.withAgency;
                    }else{
                        this.options = taskList.team.withOutAgency;
                    }
                    Task.hideLoader();
                }else if(taskList.status){
                    this.showModal = false;
                    this.itemIndex = '';
                    this.defaultTeam = this.config.defaultTeam;
                    Task.hideLoader();
                }else if(taskList.boards){
                    this.totalItem = taskList.totalItem;
                    this.boards = taskList.boards;
                    this.tasks = taskList.task;
                    this.env = taskList.env;
                    this.paypal = taskList.paypal;
                    this.currency = taskList.currency.symbol;
                    this.currency_code = taskList.currency.code;
                    this.walletBalance = taskList.walletBalance;
                    Task.hideLoader();
                }else{
                    Task.hideLoader();
                }
            },
            getClass(defCl){
                //btn-filter-drafts-active
                /*let cl = "wps_"+defCl+" ";
                if(this.defaultStatus=='all' || this.defaultStatus==''){
                    cl = cl+"active";
                }else{
                    if(this.defaultStatus==defCl){
                        cl = cl+"active";
                    }
                }
                return cl;*/

                let cl = '';
                let clStatus = '';

                switch (defCl) {
                  case 'in-progress':
                    clStatus = 'in_progress';
                    break;
                  case 're-open':
                    clStatus = 'reopen';
                    break;
                  default:
                    clStatus = defCl;
                }
                if(this.defaultStatus=='all' || this.defaultStatus==''){
                  cl = '-active';
                }else{
                  if(this.defaultStatus==clStatus){
                    cl = '-active';
                  }
                }
                return 'btn-filter-'+defCl+cl;
            },
            /**/
            async sendMsg(msg) {
                setTimeout(function() {
                    sendMessage(msg);
                },1500);
            },
            taskStatusFilter(type){
                if(this.defaultStatus!=type){
                    Task.showLoader();
                    broHistory("status",type);
                    //this.currentStatus = this.defaultStatus = type;
                    if(type!='all'){
                        this.img.checkbox = 'images/uncheck.png';
                    }else{
                        this.img.checkbox = 'images/check.png';
                    }
                    Task.hideLoader();
                }
            },
            getTasks(type){
                if(this.defaultStatus!=type || (this.defaultStatus==type && this.tasks.length==0) || this.callNum<=2){
                    if(this.callNum==2 || this.callNum==3){
                        Task.showLoader();
                    }
                    broHistory("status",type);
                    this.defaultStatus = type;
                    if(type!='all'){
                        this.img.checkbox = 'images/uncheck.png';
                    }else{
                        this.img.checkbox = 'images/check.png';
                    }
                    let params = {api:"listing",token:this.currentuser.admin_token,type:type,email:this.currentuser.admin_email};
                    if(this.priority!='all'){
                        params = {api:"listing",token:this.currentuser.admin_token,type:type,email:this.currentuser.admin_email,priority:this.priority};
                    }
                    this.sendMsg(''+JSON.stringify(params));
                    if(this.callNum==0){
                        this.clearInterVal();
                    }
                }else if(this.tasks.length>0){
                    this.clearInterVal();
                }
            },
            getTaskBoard(){
                let params = {api:"task_boards",token:this.currentuser.admin_token,email:this.currentuser.admin_email};
                this.sendMsg(''+JSON.stringify(params));
            },
            /*getTaskStatus(index){
                switch (this.tasks[index].status) {
                    case 3:
                        return "estimation";   
                        break;
                    case 4:
                        return "ready";
                        break;
                    case 5:
                        return "in_progress";
                        break;
                    case 6:
                        return "review";
                        break;
                    case 7:
                        return "reopen";
                        break;
                    case 8:
                        return "done";
                        break;
                    default:
                        return "draft"; 
                }
            },*/
            manageStatus(index){
                switch (this.tasks[index].status) {
                    case 3:
                        this.taskStatus.estimation = this.taskStatus.estimation-1;    
                        break;
                    case 4:
                        this.taskStatus.ready = this.taskStatus.ready-1;
                        break;
                    case 5:
                        this.taskStatus.in_progress = this.taskStatus.in_progress-1;
                        break;
                    case 6:
                        this.taskStatus.review = this.taskStatus.review-1;
                        break;
                    case 7:
                        this.taskStatus.reopen = this.taskStatus.reopen -1;
                        break;
                    case 8:
                        this.taskStatus.done = this.taskStatus.done-1;
                        break;
                    default:
                        this.taskStatus.draft = this.taskStatus.draft-1; 
                }
            },
            deleteTask(index)
            {
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_delete",token:this.currentuser.admin_token,tId:this.tasks[index].id,email:this.currentuser.admin_email}));
                this.manageStatus(index);
                this.tasks.splice(index, 1);
                Task.hideLoader();
                /*let type = this.defaultStatus;
                this.defaultStatus = "";
                this.callNext = false;
                this.getTasks(type);*/
                             
            },
            callPopUpForEstimation(index){
                Task.showLoader();
                this.showModal = true;
                this.itemIndex = index;
                this.defaultTeam = this.config.defaultTeam;
                Task.hideLoader();
            },
            askForEstimation(index){
                /*alert(index);
                alert(this.defaultTeam);*/
                if(index!='' || index==0){
                    Task.showLoader();
                    this.sendMsg(''+JSON.stringify({api:"task_estimation",token:this.currentuser.admin_token,post:"team="+this.defaultTeam,tId:this.tasks[index].id,email:this.currentuser.admin_email}));
                    this.manageStatus(index);
                    this.tasks[index].status = parseInt(this.tasks[index].status) + 3;
                    this.taskStatus.estimation = parseInt(this.taskStatus.estimation) + 1;    
                    //Task.hideLoader();
                }
                /*
                let type = this.defaultStatus;
                this.defaultStatus = "";
                this.callNext = false;
                this.getTasks(type);*/
            },
            kanban_click_handler(el){
                Task.showLoader();
                this.$emit('update', 'task-details');
                broHistory("task",el.dataset.eid);
            },
            kanban_get_task(id,status){
                //alert(this.totalItem);
                this.totalItem = 0;
                //this.boards = [];
                Task.showLoader();
                //this.getTaskBoard();
                this.sendMsg(''+JSON.stringify({api:"task_board_status",token:this.currentuser.admin_token,post:"status="+status+"&totalItem="+this.totalItem,tId:id,email:this.currentuser.admin_email}));
            },
            kanban_delete_item(el){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_delete",token:this.currentuser.admin_token,tId:el.dataset.eid,email:this.currentuser.admin_email}));
                //Task.hideLoader();             
            },
            kanban_callPayPopup(el){
                //console.log(el.dataset)
                Task.showLoader();
                this.itemIndex = el.dataset.index;
                this.showPayModal = true;
                Task.hideLoader();
            },
            taskDetails(e){
                Task.showLoader();
                this.$emit('update', 'task-details');
                broHistory("task",jQuery(e.target).parents('tr:first').attr('id'));
            },
            viewChange(){
                //alert('as');
                Task.showLoader();
                this.$emit('update', 'task-boards');
                broHistory("boards",'all');
            },
            pay(index){
                alert("pay to paypal");
            },
            paymentAuthorized: function (data) {
                Task.showLoader();
                //console.log("payment payment-authorized");
                //console.log(data);
            },
            paymentCompleted: function (data) {
                let tdata = Base64.decode(data.transactions[0].item_list.items[0].sku);
                //console.log(data);
                //console.log(JSON.stringify({api:"task_payment",post:"txn="+data.transactions[0].related_resources[0].sale.id+"&amount="+data.transactions[0].amount.total+"&payer_id="+data.payer.payer_info.payer_id+"&task_details="+tdata}));
                this.sendMsg(''+JSON.stringify({api:"task_payment",token:this.currentuser.admin_token,post:"txn="+data.transactions[0].related_resources[0].sale.id+"&amount="+data.transactions[0].amount.total+"&payer_id="+data.payer.payer_info.payer_id+"&task_details="+tdata}));
                let type = this.defaultStatus;
                this.showPayModal = false;
                this.defaultStatus = "";
                this.callNum = 2;
                this.totalItem = 0;
                this.getTaskBoard();
                /*this.interval = setInterval(function () {

                  this.getTasks(type);
                  this.callNum = parseInt(this.callNum)-1;
                  
                  
                }.bind(this), 5000);*/
            },
            paymentCancelled: function (data) {
                Task.hideLoader();
                //console.log("payment payment-cancelled");
                //console.log(data);
            },
            priorityTasks(val){
                this.callNum = 2;
                this.getTasks(this.defaultStatus);
            },
            callPayPopup(index){
                Task.showLoader();
                this.showPayModal = true;
                this.itemIndex = index;
                //this.defaultTeam = this.config.defaultTeam;
                Task.hideLoader();
            },
            payWithWallet(index){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_payment",token:this.currentuser.admin_token,post:"amount="+this.tasks[index].price+"&pay=wallet&task_details="+(this.tasks[index].md5)}));
                let type = this.defaultStatus;
                this.showPayModal = false;
                this.defaultStatus = "";
                this.callNum = 2;
                this.totalItem = 0;
                this.getTaskBoard();
                /*this.interval = setInterval(function () {
                  this.getTasks(type);
                  this.callNum = parseInt(this.callNum)-1;
                }.bind(this), 5000);*/
            },
            taskUpdateStatus(index,status){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_status_update",token:this.currentuser.admin_token,task_status:status,tId:this.tasks[index].id,email:this.currentuser.admin_email}));
                let type = this.defaultStatus;
                this.showPayModal = false;
                this.defaultStatus = "";
                this.callNum = 2;
                this.interval = setInterval(function () {
                  this.getTasks(type);
                  this.callNum = parseInt(this.callNum)-1;
                }.bind(this), 5000);
            }
        }
    })
    Vue.component('task-details', {
        template: '#details-template',
        //template: require('./template/Details.html'),
        props: ['config','currentuser'],
        data: function() {
            return {
                task:{},
                details:{},
                threads:{},
                taskAssigned:false,
                currency:'',
                pageTitle:"Task",
                tabDiscussion:'active',
                tabWorker:'',
                showEstimationModal:false,
                task_id:"",
                message:"",
                options:[],
                defaultTeam:this.config.defaultTeam,
                showModal:false,
                itemIndex:'',
                auth:{email:this.currentuser.admin_email}
            }
        },
        mounted () {
            console.log(this.currentuser);
            var uri = window.location.toString();
            if (uri.indexOf("#") > 0) {
                var name = uri.substring(uri.indexOf("#task:")).split('#task:');
                if(name[1])
                    this.task_id = name[1];
            }
            if(parseInt(this.task_id).length==0){
                this.backToListing();
            }
            this.getTask();
            this.bindEvent(window, 'message',this.getResponse);
        },
        computed: {   
            isWorker: function () {
              return false;
            },
            isAgency: function () {
              return false;
            },       
            WP: function(){
                return true;
            }      
        },
        methods: {
            async bindEvent(element, eventName, eventHandler) {
                if (element.addEventListener){
                    element.addEventListener(eventName, eventHandler, false);
                } else if (element.attachEvent) {
                    element.attachEvent('on' + eventName, eventHandler);
                }
            },
            getResponse(e){
                let response = jQuery.parseJSON(e.data);
                
                if(response.task){
                    Task.hideLoader();
                    this.task = response.task;
                    this.threads = response.threads;
                    this.taskAssigned = response.taskAssigned;
                    this.details = response.details;
                    this.currency = response.currency;
                    if(this.config.assignAgency){
                        this.options = response.team.withAgency;
                    }else{
                        this.options = response.team.withOutAgency;
                    }
                }else if(response.status){
                    Task.hideLoader();                    
                    this.task.status = response.status;
                    this.task.status_name = response.status_name;
                    this.showModal = false;
                    this.defaultTeam = this.config.defaultTeam;
                }else if(response.sendmessage){
                    Task.hideLoader();
                }
            },
            sendMsg(msg) {
                setTimeout(function() {
                    sendMessage(msg);
                },500);
            },
            isAdmin: function () {
              return false;
            },
            isClient: function () {
              return true;
            },  
            getClass(status){
                let className = '';
                switch (status) {
                    case 3:
                        className = 'wpsBadge_estimation';
                        break;
                    case 4:
                        className = 'wpsBadge_ready';
                        break;
                    case 5:
                        className = 'wpsBadge_in_progress';
                        break;
                    case 6:
                        className = 'wpsBadge_review';
                        break;
                    case 7:
                        className = 'wpsBadge_reopen';
                        break;
                    case 8:
                        className = 'wpsBadge_done';
                        break;
                    default:
                        className = 'wpsBadge_draft';
                }
                return className;
            },
            getTask(){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_details",token:this.currentuser.admin_token,email:this.currentuser.admin_email,tId:this.task_id}));
            },
            backToListing(){
                this.$emit('update', 'task-boards');
                //broHistory("boards","all");
                broHistory("status","all");
            },
            askForEstimation(){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_estimation",token:this.currentuser.admin_token,post:"team="+this.defaultTeam,tId:this.task.id,email:this.currentuser.admin_email}));
            },
            callPopUpForEstimation(){
                Task.showLoader();
                this.showModal = true;
                this.defaultTeam = this.config.defaultTeam;
                Task.hideLoader();
            },
            updateStatus(status){
                Task.showLoader();
                this.sendMsg(''+JSON.stringify({api:"task_status_update",token:this.currentuser.admin_token,task_status:status,tId:this.task.id,email:this.currentuser.admin_email}));
            },
            sendMessage(){
                if(this.message){
                    Task.showLoader();
                    this.sendMsg(''+JSON.stringify({api:"send_message",token:this.currentuser.admin_token,post:"message="+this.message,tId:this.task.id,email:this.currentuser.admin_email}));
                    this.message = "";
                    this.getTask();
                }
            },
        }
    });

    // DECLARATION CAPITALIZE FILTER
    Vue.filter('capitalize', function (value) {
        if (!value) return ''
          value = value.toString().replace("_", " ");
        return value.charAt(0).toUpperCase() + value.slice(1).toLowerCase()
    });
    // DECLARATION FORMATDATE FILTER
    Vue.filter('formatDate', function (value,format='DD MMM,YYYY') {
        if (value) {
            return moment(String(value)).format(format);
        }
    });
    // DECLARATION FORMATDATE FILTER
    Vue.filter('priceFormat', function (price) {
        return parseFloat(price).toFixed(2);
    });
    Vue.filter('showPriceFormat', function (price,currency) {
        return currency+parseFloat(price).toFixed(2);
    });
    
    new Vue({
        el: '#app',
        data: {
            currentView: null,
            config:{'baseUrl':vue_js_object.baseUrl,'pluginUrl':vue_js_object.pluginUrl,'defaultTeam':vue_js_object.defaultTeam},
            currentUser:jQuery.parseJSON(vue_js_object.currentUData)
        },
        mounted(){
            var uri = window.location.toString();
            //var defaults = {tab:type,url:window.location.href}
            if (parseInt(uri.indexOf("#"))>0) {
                var name = uri.substring(uri.indexOf("#")+1);
                if(name.split(':')[0]=='task')
                    this.currentView = 'task-details';
                else if(name.split(':')[0]=='boards')
                    this.currentView = 'task-boards';
                else
                    this.currentView = 'task-listing';
            }else{
                this.currentView = 'task-listing';//'task-boards';
            }
        },
        methods:{
            changeComponent(name){
                this.currentView = name;
            },
            
        },
        /*filters: {
           // Filter definitions
            Upper(value) {
                return value.toUpperCase();
            },
        }*/
    });