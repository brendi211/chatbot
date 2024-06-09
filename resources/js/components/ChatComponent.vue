<template>
    <div style="display: flex;flex-direction: column;align-items: center;">
        <ul class="ChatLog" ref="chatLog">
            <li class="ChatLog__entry" v-for="message in messages" :class="{'ChatLog__entry_mine': message.isMine}">
                {{ message.user}}
                <p class="ChatLog__message">
                    {{ message.text }}
                </p>
                <div v-if="message.original?.type === 'actions'" style="position:relative;">
                    <button class="btn" v-for="action in message.original.actions"
                         @click="performAction(action.value, message.original)">
                        {{ action.text }}
                    </button>
                </div>
            </li>
        </ul>
        <div>
            <input style="border: 1px solid black" type="text" class="ChatInput" @keyup.enter="sendMessage" v-model="newMessage" placeholder="Введіть повідомлення">
            <button style="display: inline" class="btn" type="button" @click="sendMessage(newMessage)">Надіслати</button>
        </div>
    </div>
</template>

<style>
.ChatAttachment+label {
    cursor: pointer;
    height: 25px;
    display: inline-block;
    border-radius: 5px;
    background-color: white;
    border: none;
    padding: 10px;
}
input.ChatInput {
    width: 300px;
    border-radius: 5px;
    border: none;
    padding: 10px;
}

.btn {
    position: relative;
    padding: .5em;
    border-radius: 4px;
    display: block;
    margin: 5px;
    min-width: 100px;
    background-color: #7ec1e0;
    border: 1px solid #69b7da;
    cursor: pointer;
}
.btn:before{
    position: absolute;
    right: auto;
    bottom: .6em;
    left: -12px;
    height: 0;
    content: '';
    border: 6px solid transparent;
    border-right-color: #7ec1e0;
    z-index: 2;
}

ul.ChatLog {
    list-style: none;
    padding-left: 0;
    max-height: 50vh;
    overflow: auto;
}

.ChatLog {
    max-width: 28em;
    margin: 0 auto;
}
.ChatLog .ChatLog__entry {
    margin: .5em;
}

.ChatLog__entry {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    max-width: 100%;
}

.ChatLog__entry.ChatLog__entry_mine {
    flex-direction: row-reverse;
}

.ChatLog__entry .ChatLog__message {
    position: relative;
    margin: 0 12px;
}

.ChatLog__entry .ChatLog__message::before {
    position: absolute;
    right: auto;
    bottom: .6em;
    left: -12px;
    height: 0;
    content: '';
    border: 6px solid transparent;
    border-right-color: #ddd;
    z-index: 2;
}

.ChatLog__entry.ChatLog__entry_mine .ChatLog__message::before {
    right: -12px;
    bottom: .6em;
    left: auto;
    border: 6px solid transparent;
    border-left-color: #08f;
}

.ChatLog__message {
    background-color: #c2e2b1;
    padding: .5em;
    border-radius: 4px;
    font-weight: lighter;
    max-width: 70%;
}

.ChatLog__entry.ChatLog__entry_mine .ChatLog__message {
    border-top: 1px solid #07f;
    border-bottom: 1px solid #07f;
    background-color: #08f;
    color: #fff;
}
</style>

<script>
export default {
    props: {
        apiEndpoint: {
            default: '/botman',
        },
        userId: {
            default: +(new Date()),
        },
        smessages: {
            type: Array,
            required:false,
        }
    },

    data() {
        return {
            newMessage: null,
            messages: [],
        };
    },
    mounted() {
        this.$props.smessages.forEach((msg) => {
            this.messages.push(msg)
        })

    },
    computed: {
        reversedMessages() {
            return this.messages.slice().reverse();
        }
    },
    methods: {
        callAPI(text, interactive = false, attachment = null, callback) {
            let data = new FormData();
            const postData = {
                driver: 'web',
                userId: this.userId,
                message: text,
                attachment,
                interactive
            };

            Object.keys(postData).forEach(key => data.append(key, postData[key]));

            axios.post(this.apiEndpoint, data).then(response => {
                const messages = response.data.messages || [];
                messages.forEach(msg => {
                    this._addMessage(msg.text, msg.attachment, false, msg);
                });
                if (callback) {
                    callback(response.data);
                }
            }).finally(fin => {
                const chatLog = this.$refs.chatLog;

                // Автоматично прокручуємо блок вниз
                chatLog.scrollTop = chatLog.scrollHeight+50;
            });
        },

        performAction(value, message) {
            this.callAPI(value, true, null, (response) => {
                message.actions = null;
            });
        },

        _addMessage(text, attachment, isMine, original = {}) {
            this.messages.push({
                'isMine': isMine,
                'user': isMine ? '👨' : '🤖',
                'text': text,
                'original': original,
                'attachment': attachment || {},
            });
            const chatLog = this.$refs.chatLog;

            // Автоматично прокручуємо блок вниз
            chatLog.scrollTop = chatLog.scrollHeight+50;
        },

        sendMessage() {
            let messageText = this.newMessage;
            this.newMessage = '';
            if (messageText === 'clear') {
                this.messages = [];
                return;
            }

            this._addMessage(messageText, null, true);

            this.callAPI(messageText);
        },
        handleScroll(event) {
            const chatLog = this.$refs.chatLog;
            const isScrolledToBottom = chatLog.scrollHeight - chatLog.clientHeight <= chatLog.scrollTop + 1;

            if (isScrolledToBottom) {
                // Якщо користувач прокрутив до низу, автоматично прокручуємо до низу
                chatLog.scrollTop = chatLog.scrollHeight - chatLog.clientHeight;
            }
        }
    }
}
</script>
