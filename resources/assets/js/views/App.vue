<template>
    <div>
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <router-link :to="{ name: 'home' }" class="navbar-brand">GuestBook</router-link>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->

                        <li v-if="!user"><router-link :to="{ name: 'login' }">Login</router-link></li>
                        <li v-if="!user"><router-link :to="{ name: 'register' }">Register</router-link></li>

                        <li v-if="user" class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ user.name}} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a @click.prevent="logout()">Logout</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
        <router-view :user.sync="user"></router-view>
    </div>
</template>
<script>
    export default {
        data(){
            return {
                user: ""
            }
        },
        mounted() {
            this.checkUser()
        },
        methods: {
            checkUser(){
                var token = localStorage.getItem('token');
                if (token){
                    axios.post('/check')
                        .then(response=>{
                            this.user = response.data
                        })
                } else {
                    localStorage.clear()
                }
            },
            logout(){
                axios.post('/api/logout')
                    .then(response=>{
                        console.log(response)
                        this.user = ''
                        localStorage.removeItem('token')
                    })
            }
        }
    }
</script>