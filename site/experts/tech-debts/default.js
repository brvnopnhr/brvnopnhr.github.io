var dispatchKeyboardEvent = function(target, initKeyboradEvent_args) {
  var e = document.createEvent("KeyboardEvents");
  e.initKeyboardEvent.apply(e, Array.prototype.slice.call(arguments, 1));
  target.dispatchEvent(e);
};

var isStart = false;

var player = new PreziPlayer('player-api-intro', {
			preziId: '3sggb7gkpmzt',
			width: '100%',
			height: '100%',
			explorable: true,
			controls: false,
			debug: false
		});
		
player.on(PreziPlayer.EVENT_STATUS, function(e) { 
	if(player.getStatus() == PreziPlayer.STATUS_CONTENT_READY){
		player.play(100);
		isStart = true;
		
	}
});

player.on(PreziPlayer.EVENT_CURRENT_STEP, function(e) { 
	if(player.getCurrentStep() == 1 && isStart){
		player.stop();
		isStart = false;
		player.flyToStep(0);
		
	}
});

document.getElementById("linkHome").onclick = function(e){
	var homeSteps = [0, 3, 6, 9, 12, 15,18, 21, 24, 27, 30, 33, 36, 39, 42, 45, 48, 51, 54, 57, 60, 63, 66];
	if($.inArray(player.getCurrentStep(), homeSteps) == -1)
	{
		e.preventDefault();
		player.flyToPreviousStep();	
	}
	
};

