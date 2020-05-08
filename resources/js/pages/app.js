import TypeIt from "typeit";

let typeit = new TypeIt('#console', {

	// Make sure we scroll automatically during the animation
	afterStep: async (step, instance) => {

		window.scrollTo(0, document.body.scrollHeight);
	},

	// Remove cursor once we're done typing
	afterComplete: async (step, instance) => {

		let cursors = document.getElementsByClassName('ti-cursor');

		for (let cursor of cursors) {
			cursor.remove();
		}
	}
})
	.pause(1000)
	.type('You sit at a polished oak desk and browse GitHub repositories on your laptop. An interesting project catches your eye.', {speed: 1})
	.break()
	.break()
	.type('"Trogserve," the title reads, "A multi-player text adventure server built on top of Trogdor-pp and PHP."', {speed: 1})
	.break()
	.break()
	.type('> ', {speed: 1})
	.pause(10000)
	.type('what is trogserve?', {speed: 75})
	.pause(1500)
	.break()
	.break()
	.type('Trogserve is a love letter to both the single player text adventures and text-based MUDs of the eighties and nineties. Built on top of trogdor-pp, trogdord, and PHP, Trogserve makes it easy to host online text-based multi-player games.', {speed: 1})
	.break()
	.break()
	.type('> ', {speed: 1})
	.pause(10000)
	.type('get started', {speed: 75})
	.pause(1500)
	.break()
	.break()
	.type('&nbsp;&nbsp;&nbsp;* <a href="/games">Play a game</a>', {speed: 1})
	.break()
	.type('&nbsp;&nbsp;&nbsp;* <a href="https://github.com/crankycyclops/trogserve">View Trogserve on GitHub</a>', {speed: 1})
	.go();
