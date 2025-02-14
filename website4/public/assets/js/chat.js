// chat container setup for maximum verwirrung weil ich kann das alles langsam nicht mehr junge die sprach macht kei sinn
const chatContainer = document.getElementById('chat-container');
const chatLog = document.getElementById('chat-log');
const userInput = document.getElementById('user-input');
let hasGreeted = false; // flag so we only say grǔtzi eimal und ned sächzgs mal junge das wäri fett weird

// toggle chat open and closed again ????????????? i dont remember what i haben gesoffen hier
function toggleChat() {
    if (chatContainer.classList.contains('open')) {
        chatContainer.classList.remove('open');
        setTimeout(() => {
            chatContainer.style.visibility = 'hidden';
        }, 400); // match delay with css so it doesnt look wie abfall
    } else {
        chatContainer.style.visibility = 'visible';
        chatContainer.classList.add('open');
        
        // the actuall saying grǔtzi miternaand gugus
        if (!hasGreeted) {
            displayMessage('Virtual Assistant', "Grǔtzi 🇨🇭 ! I'm your virtual assistant! Please tell me about your problem or what you need help with.");
            hasGreeted = true;
        }
    }
}

// send message when enter key is pressed because who will press a button to send a message ???? are you a psychopat ?
userInput.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
});

// handle sending messages
function sendMessage() {
    const userMessage = userInput.value.trim();
    if (userMessage) {
        displayMessage('You', userMessage);
        userInput.value = '';
        setTimeout(() => catReply(userMessage), 1000); // delay for realism so the "assistant" can "think" about the reply
    }
}

// put a message in the chat log will chat funktioniert ned ohni message history und so lol
function displayMessage(sender, message) {
    const messageElement = document.createElement('div');
    messageElement.classList.add('chat-message');

    const senderSpan = document.createElement('span');
    senderSpan.classList.add('sender');
    senderSpan.classList.add(sender === 'You' ? 'user' : 'virtual-assistant');
    senderSpan.textContent = sender + ': ';
    
    const messageSpan = document.createElement('span');
    messageSpan.textContent = message;
    
    messageElement.appendChild(senderSpan);
    messageElement.appendChild(messageSpan);
    chatLog.appendChild(messageElement);
    chatLog.scrollTop = chatLog.scrollHeight;
}

// cat assistant category based matched replies
function catReply(userMessage) {
    const keywords = {
        "help": ["meeeooow...? figure it out yourself! because that sounds like a you problem bahahahasdjkasadlkj 💀", "meeooww! welp too bad. help yourself bahasjdhaskjdhaskjdh", "mrrrow! idk either lol. go ask yourself maybe your smart if you use your brain instread of relying on a virtual assistant cat"],
        "problem": ["meeooow, eh thats normal here, should've chosen someone else to host your stuff ngl lol", "mrow, contact sysadminier maybe idk? i am just cat or a teaput ???? idk what i am help aaaaaaa", "mreeow, tell me more ig... if you want idc lol"],
        "default": ["meow", "meeooow!", "mew.", "mrow!", "meeow...", "meow, meow.", "mroww", "mlem"]
    };

    // generate a bs reply to fool user into thinking the cat assistant is actually doing something
    function generateScrambledReply() {
        const phrases = keywords["default"];
        let scrambled = [];
        const length = Math.floor(Math.random() * 3) + 3; // random length of reply 3-5 words
        
        for (let i = 0; i < length; i++) {
            const randomPhrase = phrases[Math.floor(Math.random() * phrases.length)];
            scrambled.push(randomPhrase);
        }
        return scrambled.join(" ");
    }

    // check for keywords in user message and decide what kind of blödsinn to say zu the schafseckel using the chat
    let reply;
    if (userMessage.toLowerCase().includes("help")) {
        reply = keywords["help"][Math.floor(Math.random() * keywords["help"].length)];
    } else if (userMessage.toLowerCase().includes("problem")) {
        reply = keywords["problem"][Math.floor(Math.random() * keywords["problem"].length)];
    } else {
        reply = generateScrambledReply(); // generate scrambled reply for "default" case
    }

    displayMessage('Virtual Assistant', reply);
}