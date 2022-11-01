<template>
    <AuthenticatedLayout>
        <!-- This is an example component -->
        <div class="container mx-auto shadow-lg rounded-lg">
            <!-- header -->
            <div class="px-5 py-5 flex justify-between items-center bg-white border-b-2">
                <div class="font-semibold text-2xl">GoingChat</div>
                <div class="w-1/2">
                    <input
                        type="text"
                        name=""
                        id=""
                        placeholder="search IRL"
                        class="rounded-2xl bg-gray-100 py-3 px-5 w-full"
                    />
                </div>
                <div v-if="Object.keys(currentUser).length !== 0" class="h-12 w-12 p-2 bg-yellow-500 rounded-full text-white font-semibold flex items-center justify-center">
                    {{  currentUser.name.split(' ').map((item) => item.charAt(0)).join('').toUpperCase() }}
                </div>
                <div v-else class="h-12 w-12 p-2 bg-yellow-500 rounded-full text-white font-semibold flex items-center justify-center"></div>
            </div>
            <!-- end header -->


            <!-- Chatting -->
            <div class="flex flex-row justify-between bg-white">

                <!-- user list -->
                <div class="flex flex-col w-2/5 border-r-2 overflow-y-auto">
                    <div v-for="(user, uIdx) in users" :key="'user'+uIdx" class="flex flex-row py-4 px-2 justify-center items-center border-b-2 cursor-pointer"
                         @click.prevent="setCurrentUser(user)"
                         :class="currentUser && currentUser.id === user.id ? 'border-l-4 border-blue-400' : notification && notification?.from?.id === user.id ? 'border-l-4 border-red-400' : ''">
                        <div class="w-1/4">
                            <div class="h-12 w-12 p-2 bg-yellow-500 rounded-full text-white font-semibold flex items-center justify-center">
                                {{ user.name.split(' ').map((item) => item.charAt(0)).join('').toUpperCase() }}
                            </div>
                        </div>

                        <div class="w-full">
                            <div class="text-lg font-semibold">{{ user.name }}</div>
                            <span class="text-gray-500">{{ user.email }}</span>
                        </div>
                    </div>
                </div>
                <!-- end user list -->

                <!-- message -->
                <div class="w-full px-5 flex flex-col justify-between">
                    <div v-for="(message, mIdx) in messages" :key="'message'+mIdx" class="flex flex-col mt-5">

                        <!-- me -->
                        <div v-if="message.sender === this.$page.props.auth.user.id" class="flex justify-end mb-4">
                            <div class="mr-2 py-3 px-4 bg-blue-400 rounded-bl-3xl rounded-tl-3xl rounded-tr-xl text-white">
                                {{ message.message }}
                            </div>
                            <div class="h-12 w-12 p-2 bg-yellow-500 rounded-full text-white font-semibold flex items-center justify-center">
                                {{ this.$page.props.auth.user.name.split(' ').map((item) => item.charAt(0)).join('').toUpperCase() }}
                            </div>
                        </div>

                        <!-- you -->
                        <div v-if="message.sender === currentUser.id" class="flex justify-start mb-4">
                            <div class="h-12 w-12 p-2 bg-yellow-500 rounded-full text-white font-semibold flex items-center justify-center">
                                {{ currentUser.name.split(' ').map((item) => item.charAt(0)).join('').toUpperCase() }}
                            </div>
                            <div class="ml-2 py-3 px-4 bg-gray-400 rounded-br-3xl rounded-tr-3xl rounded-tl-xl text-white">
                                {{ message.message }}
                            </div>
                        </div>

                    </div>

                    <!-- message input -->
                    <form @submit.prevent="submit">
                        <div class="py-5 flex flex-row">
                            <input v-model="form.message" class="w-full bg-gray-300 py-5 px-3 rounded-l-lg" type="text" placeholder="type your message here..."/>
                            <button type="submit" class="bg-blue-500 text-white py-5 px-3 rounded-r-lg">Send</button>
                        </div>
                    </form>

                </div>
                <!-- end message -->

                <div class="w-2/5 border-l-2 px-5">
                    <div class="flex flex-col">
                        <div class="font-semibold text-xl py-4">Mern Stack Group</div>
                        <img
                            src="https://source.unsplash.com/L2cxSuKWbpo/600x600"
                            class="object-cover rounded-xl h-64"
                            alt=""
                        />
                        <div class="font-semibold py-4">Created 22 Sep 2021</div>
                        <div class="font-light">
                            Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt,
                            perspiciatis!
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

export default {
    name: "Chat",
    components: {
        AuthenticatedLayout
    },

    props: {
        users: Object
    },

    data() {
        return {
            messages: [],
            notification: {},
            currentUser: {},
            form: {
                receiver: '',
                message: ''
            }
        }
    },

    created() {
        Echo.private('notification.' + this.$page.props.auth.user.id)
            .notification((notification) => {
                this.notification = notification;
            });

        Echo.private('messages.' + this.$page.props.auth.user.id)
            .listen('.messages.chat', (e) => {
                this.messages.push(e.message);
            });
    },

    methods: {
        //Set current user
        setCurrentUser(user){
            this.currentUser = user;
            this.form.receiver = user.id;
            this.notification = {};
            this.getMessages();
        },

        //Get previous conversations

        async getMessages(){
            return await axios.get(route('ajax.messages.get', this.currentUser.id)).then((response) => {
                if (response.data.status === 200){
                    this.messages = response.data.data;
                }
            })
        },

        //Submit message
        async submit(){
            if (this.form.message !== '' && this.form.receiver !== ''){
                return await axios.post(route('ajax.chat.store'), this.form)
                    .then((response) => {
                        if (response.data.status === 200){
                            this.form.message = '';
                            this.messages.push(response.data.data);
                        }
                    })
            }
        }
    },


}
</script>

<style scoped>

</style>
