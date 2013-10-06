process.stdin.resume();
process.stdin.setEncoding('utf8');

var json = "";
process.stdin.on('data', function(chunk) {
    json += chunk;
});

process.stdin.on('end', function () {
    process.stdout.write(JSON.stringify(JSON.parse(json), null, '    '));
});
