<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Login</div>

                    <div class="panel-body">

                        <div v-if="errorMessages" class="alert alert-danger" role="alert">
                            Login failed
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" @submit.prevent="loginUser">

                            <div class="form-group">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="login.email" required autofocus>
                                    <span v-if="errorMessages.email" class="help-block">
                                        <strong>{{ errorMessages.email[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="login.password" required>
                                    <span v-if="errorMessages.password" class="help-block">
                                        <strong>{{ errorMessages.password[0] }}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Login",
        props: ['user'],
        data(){
            return {
                login:{
                    email: "",
                    password: ""
                },
                errorMessages:"",
                // user:""
            }
        },
        mounted() {
            this.checkUser()
        },
        methods: {
            loginUser(){
                axios.post('/login', this.login)
                    .then(response=>{
                        var token = response.data.token;
                        if(!token){
                            this.errorMessages = response.data.message;
                        } else {
                            localStorage.setItem('token',token)
                            this.checkUser()
                        }
                    })
            },
            checkUser(){
                var token = localStorage.getItem('token');
                if (token){
                    window.axios.defaults.headers.common['Authorization'] = `bearer ${token}`;
                    axios.post('/check')
                        .then(response=>{
                            this.$emit('update:user', response.data)
                            this.goHome()
                        })
                }
            },
            goHome(){
                this.$nextTick(() => this.$router.push({ name: "home" }));
            }
        }
    }
</script>

<style scoped>

</style>