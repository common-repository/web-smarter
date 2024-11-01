var CanvasDrawer = (function($,w){
	var cd = function(){
		this.params = {
			canvasWrapper:'canvas-container',
			canvas : null,
			selection : false,
			isDown : true,
			line: true,
			triangle: true,
			drawerType : null,
			typeObj:true,
			fabricObj: null,
			strokeColor:'purple',
			fillColor:'',
			angle:0,
			strokeWidth:2,
			fontSize:20,
			strEditText:"Edit HERE!"
		}
	}
	cd.prototype = {
		init:function(){
			this.events();
		},
		events:function(){
			var self = this;
		},
		bindEvents : function() {
			var self = this;
			var inst = this.params;
			inst.canvas.selection = inst.selection;
			inst.canvas.on('mouse:down',function(e){
				self.onMouseDown(e);
				if(!inst.canvas.getActiveObject())
			    {
			    	self.removeDeleteBtn(); 
			    }
			});
			inst.canvas.on('mouse:move',function(e){
				self.onMouseMove(e);
			});
			inst.canvas.on('mouse:up',function(e){
				self.onMouseUp(e);
				//var o = this.params.canvas.getObjects();
				//self.addDeleteBtn(o.target.oCoords.mt.x, o.target.oCoords.mt.y, o.target.width);
			});

			inst.canvas.on('object:moving',function(e){
				//self.removeDeleteBtn();
			});
			inst.canvas.on("object:modified",function(e){
				self.onModified(e);
				//self.addDeleteBtn(e.target.oCoords.mt.x, e.target.oCoords.mt.y, e.target.width);
			});
		    inst.canvas.on("object:scaling", function (e) {
		    	//self.removeDeleteBtn();
		    });
		    inst.canvas.on("object:rotating", function (e) {
		    	//self.removeDeleteBtn();
		    });
		    inst.canvas.on('object:selected',function(e){
				//self.removeDeleteBtn();
				//self.addDeleteBtn(e.target.oCoords.mt.x, e.target.oCoords.mt.y, e.target.width);
			});
			inst.canvas.renderAll();

			window.addEventListener("keydown", event=>{
			  // delete
			  if(event.keyCode === 46){
			    event.preventDefault();

			    // this.canvas.preserveObjectStacking = false;
			    this.removeRmoveObject();
			    
			    /*let selection;
			    if(this.params.canvas.getActiveObject()){
			    	selection = [this.params.canvas.getActiveObject()];
			  	}else if(this.params.canvas.getActiveGroup()){
			  		selection = [this.params.canvas.getActiveGroup().getObjects()];
			  	}
			    selection.forEach(obj => obj.remove());
			    // this.canvas.preserveObjectStacking = true;
				this.params.canvas.discardActiveGroup();
			    this.params.canvas.renderAll();*/
			  }
			});
		},
		set defaultStrokeColor(val='purple'){
			this.params.strokeColor = val;
		},
		get defaultStrokeColor(){
			return this.params.strokeColor;
		},
		get canvasObj(){
			return this.params.canvas;
		},
		set canvasObj(val){
			this.params.canvas = val;
		},
		get drawerType(){
			return this.params.drawerType;
		},
		set drawerType(val){
			this.params.drawerType = val;
		},
		get objSelection(){
			return this.params.selection;
		},
		set objSelection(val){
			this.params.selection = val;
		},
		_FabricCalcArrowAngle : function(x1, y1, x2, y2) {
	        var angle = 0, x, y;
	        x = (x2 - x1);
	        y = (y2 - y1);
	        if (x === 0) {
	            angle = (y === 0) ? 0 : (y > 0) ? Math.PI / 2 : Math.PI * 3 / 2;
	        } else if (y === 0) {
	            angle = (x > 0) ? 0 : Math.PI;
	        } else {
	            angle = (x < 0) ? Math.atan(y / x) + Math.PI :
	                (y < 0) ? Math.atan(y / x) + (2 * Math.PI) : Math.atan(y / x);
	        }
	        return (angle * 180 / Math.PI + 90);
	    },
	    generateUUID : function(){
		    var d = new Date().getTime();
		    if(window.performance && typeof window.performance.now === "function"){
		        d += performance.now(); //use high-precision timer if available
		    }
		    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		        var r = (d + Math.random()*16)%16 | 0;
		        d = Math.floor(d/16);
		        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
		    });
		    return uuid;
		},
		setStroke : function(param){
			var defaults = $.extend({color:this.params.strokeColor},param);
			if(this.params.fabricObj!=null){
				this.params.strokeColor = defaults.color;
				selectedObject = this.params.canvas.getActiveObject();
				if(selectedObject){
					if(selectedObject.type !== 'i-text'){
						selectedObject.set("stroke", defaults.color);
					}
	            	if(selectedObject.type === 'arrow' || selectedObject.type === 'i-text'){ 
	            		selectedObject.set("fill", defaults.color);
	            	}
	            	if(selectedObject.type === 'arrow'){
	            		this.params.line.set("stroke", defaults.color);
	            		this.params.triangle.set("stroke", defaults.color);
	            	}
	            	this.params.canvas.renderAll();
	            }	
			}
		},
		setFontSize : function(param){
			var defaults = $.extend({size:this.params.fontSize},param);
			if(this.params.fabricObj!=null){
				this.params.strokeColor = defaults.size;
				selectedObject = this.params.canvas.getActiveObject();
				if(selectedObject && selectedObject.type === 'i-text'){
					selectedObject.setFontSize(defaults.size);
	            	this.params.canvas.renderAll();
	            }	
			}
		},
		removeDeleteBtn:function(){
			$('.'+this.params.canvasWrapper).find(".deleteBtn").remove();
		},
		removeRmoveObject:function(){
			if(this.params.canvas.getActiveObject()){
				this.params.canvas.remove(this.params.canvas.getActiveObject());
				this.removeDeleteBtn();
			}
		},
		addDeleteBtn:function(x, y, w){
			//alert("asdasd");
			//$(".deleteBtn").remove(); 
			var inst = this;
			$('.'+this.params.canvasWrapper).find(".deleteBtn").remove();
			var btnLeft = x;
			var btnTop = y - 25;
			var widthadjust=w/2;
			btnLeft=widthadjust+btnLeft-10;
			var deleteBtn = '<img src="https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/256x256/DeleteRed.png" class="deleteBtn" style="position:absolute;top:'+btnTop+'px;left:'+btnLeft+'px;cursor:pointer;height:20px;width:20px;"/>';
			$('.'+this.params.canvasWrapper).append(deleteBtn);
			$('.deleteBtn').bind('click',function(){
				inst.removeRmoveObject();
			});
		},
		drawObject : function(pointer){
			origX = pointer.x;
		    origY = pointer.y;
		    var fabObj;
			switch(this.params.drawerType){
				case "reactangle":
					fabObj = new fabric.Rect({
											left:origX,
											top:origY,
											width: pointer.x - origX,
											height: pointer.y - origY,
											angle: this.params.angle,
											fill: this.params.fillColor,
									        stroke : this.params.strokeColor,
									        type : 'rect',
									        strokeWidth:this.params.strokeWidth,
									        uuid : this.generateUUID()
										});
					break;
				case "circle":
					fabObj = new fabric.Ellipse({
											left: origX,
						                    top: origY,
						                    originX: 'left',
						                    originY: 'top',
						                    rx: pointer.x - origX,
						                    ry: pointer.y - origY,
											angle: this.params.angle,
											fill: this.params.fillColor,
									        stroke : this.params.strokeColor,
									        type : 'ellipse',
									        strokeWidth:this.params.strokeWidth,
									        uuid : this.generateUUID()
										});
					break;
				case "text" :
					/*fabObj = new fabric.IText('Tap and Type', { 
						      left: 50,
						      top: 100,
						      lockUniScaling: true,
						      fontFamily: 'arial black',
						      fill: 'red',
							    fontSize: 50
						});*/
					fabObj = new fabric.IText(this.params.strEditText, { 
						      left: origX,
						      top: origY,
						      lockUniScaling: true,
						      fontFamily: 'arial black',
						      fill: this.params.strokeColor,
							  fontSize: this.params.fontSize
						});
					break;
				case "arrow":
					var points = [pointer.x, pointer.y, pointer.x, pointer.y];
		            line = new fabric.Line(points, {
								                strokeWidth: this.params.strokeWidth,
								                fill: this.params.strokeColor,
								                stroke: this.params.strokeColor,
								                originX: 'center',
								                originY: 'center',
								                id:'arrow_line',
								                uuid : this.generateUUID(),
								                type : 'arrow'
								            });
		            var centerX = (line.x1 + line.x2) / 2;
		            var centerY = (line.y1 + line.y2) / 2;
		            deltaX = line.left - centerX;
		            deltaY = line.top - centerY;

		            triangle = new fabric.Triangle({
					                left: line.get('x1') + deltaX,
					                top: line.get('y1') + deltaY,
					                originX: 'center',
					                originY: 'center',
					                selectable: false,
					                pointType: 'arrow_start',
					                angle: -45,
					                width: 20,
					                height: 20,
					                fill: this.params.strokeColor,
					                id:'arrow_triangle',
					                uuid : line.uuid
					            });
		            fabObj = {'line':line,'triangle':triangle};
					break;
			}
			return fabObj;			
		},
		onMouseDown : function(o){
			var inst = this.params;
			inst.isDown = true;
			var pointer = inst.canvas.getPointer(o.e);
			origX = pointer.x;
		    origY = pointer.y;
			if(inst.drawerType !== null){
				switch(inst.drawerType){
					case "arrow":
						var drawObj = this.drawObject(pointer);
						inst.typeObj = inst.line = drawObj.line;
						inst.typeObj = inst.triangle = drawObj.triangle;
						inst.canvas.add(drawObj.line,drawObj.triangle);
						break;
					case "text":
						inst.typeObj = this.drawObject(pointer);
						inst.canvas.add(inst.typeObj);
						break;
					default:
						inst.typeObj = this.drawObject(pointer);
						inst.canvas.add(inst.typeObj).setActiveObject(inst.typeObj);
						break;
				}
				inst.fabricObj = inst.typeObj;

			}
		},
		onMouseMove : function(o) {
			var inst = this.params;
		    if (inst.isDown) {
			    var pointer = inst.canvas.getPointer(o.e);
			    var rect = inst.fabricObj;
			    if(rect===null){
			    	return;
			    }
			    switch(this.params.drawerType){
					case "reactangle":
						if(origX>pointer.x){
					        rect.set({ left: Math.abs(pointer.x) });
					    }
					    if(origY>pointer.y){
					        rect.set({ top: Math.abs(pointer.y) });
					    }
					    
					    rect.set({ width: Math.abs(origX - pointer.x) });
					    rect.set({ height: Math.abs(origY - pointer.y) });
						break;
					case "text":
						if(origX>pointer.x){
					        rect.set({ left: Math.abs(pointer.x) });
					    }
					    if(origY>pointer.y){
					        rect.set({ top: Math.abs(pointer.y) });
					    }
					    
					    rect.set({ width: Math.abs(origX - pointer.x) });
					    rect.set({ height: Math.abs(origY - pointer.y) });
						break;
					case "circle":
						ellipse = rect;
						if(ellipse === null) {
				            return;
				        }
				        var rx = Math.abs(origX - pointer.x)/2;
				        var ry = Math.abs(origY - pointer.y)/2;
				        if (rx > ellipse.strokeWidth) {
				            rx -= ellipse.strokeWidth/2;
				        }
				        if (ry > ellipse.strokeWidth) {
				            ry -= ellipse.strokeWidth/2;
				        }
				        ellipse.set({ rx: rx, ry: ry});

				        if(origX > pointer.x){
				            ellipse.set({originX: 'right' });
				        } else {
				            ellipse.set({originX: 'left' });
				        }
				        if(origY > pointer.y){
				            ellipse.set({originY: 'bottom'  });
				        } else {
				            ellipse.set({originY: 'top'  });
				        }
						break;
					case "arrow":
						this.params.line.set({
			                x2: pointer.x,
			                y2: pointer.y
			            });
			            this.params.triangle.set({
			                'left': pointer.x + deltaX,
			                'top': pointer.y + deltaY,
			                'angle': this._FabricCalcArrowAngle(this.params.line.x1,
			                                                this.params.line.y1,
			                                                this.params.line.x2,
			                                                this.params.line.y2)
			            });
						break;

				}
			    inst.canvas.renderAll();
		   	}
		},
		onMouseUp: function (e) {
			//if(freeDrawing) {
			this.params.isDown = false;
			if (this.params.drawerType !== null) {
				//textVal = prompt('Please enter text value..', '45');
			    if(this.params.drawerType=='arrow') {

			    	var group = new window.fabric.Group([this.params.line,this.params.triangle],
			                {
			                    //borderColor: 'black',
			                    //cornerColor: 'green',
			                    lockScalingFlip : true,
			                    //typeOfGroup : 'arrow',
			                    userLevel : 1,
			                    //name:'my_ArrowGroup',
			                    uuid : this.params.fabricObj.uuid,
			                    type : 'arrow'
			                }
			                );
			    	this.params.canvas.remove(this.params.line, this.params.triangle);// removing old object
			    	this.params.fabricObj = group;
			    	this.params.canvas.add(group);
				}
				this.params.drawerType = null;
			}
			//}
			//set coordinates for proper mouse interaction
			var objs = this.params.canvas.getObjects();
	   	    for (var i = 0 ; i < objs.length; i++) {
				objs[i].setCoords();
	   	   	}
		},
		onModified:function (e) {
	        try {
	       	 	var obj = e.target;
	       	 	if (obj) {
	   	     	    if (obj.type === 'ellipse') {
						obj.rx *= obj.scaleX;
						obj.ry *= obj.scaleY;
	   	   	     	}
	   	   	     	if (obj.type === 'i-text') {
				      obj.fontSize *= obj.scaleX;
				      obj.fontSize = obj.fontSize.toFixed(0);
				    }
		   	     	if (obj.type !== 'arrow' || obj.type !== 'i-text') {
						obj.width *= obj.scaleX;
		   	     	    obj.height *= obj.scaleY;
					}
					if (obj.type !== 'arrow') {
			   	     	obj.scaleX = 1; 
		   	     	    obj.scaleY = 1;
						obj._clearCache();
					}
	      			
					//find text with the same UUID
	   	     	    /*var currUUID = obj.uuid;
	   	     	    var objs = this.params.canvas.getObjects();
	   	     	    var currObjWithSameUUID = null;
	   	     	    for (var i = 0 ; i < objs.length; i++) {
						if (objs[i].uuid === currUUID && 
								objs[i].type === 'text') {
							currObjWithSameUUID = objs[i];
							break;
						}
	   	   	     	}
	   	   	     	if (currObjWithSameUUID) {
	   	   	     		currObjWithSameUUID.left = obj.left;
	   	   	     		currObjWithSameUUID.top = obj.top - 30;
	   	   	     		currObjWithSameUUID.opacity = 1;
	      	   	   	}*/
      	   	   	}        	   	   	
	          } catch (E) {
	          }
	   	}
	}
	var obj = new cd();
	obj.init();
	return obj;
})(jQuery,window);