const jsobf = require('javascript-obfuscator');
const fs = require('fs');
const argv = require('minimist')(process.argv.slice(2));

var file = argv.f;
var dci = argv.dci == '1' ? true : false;

var script = fs.readFileSync(file).toString();

var obfresult = jsobf.obfuscate(script, {
	compact: true,
	controlFlowFlattening: false,
	controlFlowFlatteningThreshold: 0.75,
	deadCodeInjection: dci,
	deadCodeInjectionThreshold: 0.4,
	debugProtection: false,
	debugProtectionInterval: false,
	disableConsoleOutput: true,
	domainLock: [],
	mangle: false,
	reservedNames: [],
	rotateStringArray: true,
	seed: 0,
	selfDefending: true,
	sourceMap: false,
	sourceMapBaseUrl: '',
	sourceMapFileName: '',
	sourceMapMode: 'separate',
	stringArray: true,
	stringArrayEncoding: false,
	stringArrayThreshold: 0.75,
	unicodeEscapeSequence: false
}); // https://github.com/javascript-obfuscator/javascript-obfuscator

console.log(obfresult.getObfuscatedCode());