<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>

                    <div class="panel-body">
                        <div v-if="errorMessages" class="alert alert-danger d-flex">
                            <span v-for="message in errorMessages">{{message[0]}}<br></span>
                            <button type="button" class="close" aria-label="Close" @click.prevent="errorClose()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" @submit.prevent="registerUser">

                            <div class="form-group">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" v-model="register.name" required autofocus>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" v-model="register.email" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" v-model="register.password" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" v-model="register.password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        Register
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
        name: "Register",
        data(){
            return {
                register:{
                    name: "",
                    email: "",
                    password: "",
                    password_confirmation: ""
                },
                errorMessages:''
            }
        },
        mounted() {
            this.checkUser()
        },
        methods: {
            registerUser(){
                axios.post('/register', this.register)
                    .then(response=>{
                        var token = response.data.token;
                        if(!token){
                            this.errorMessages = response.data.errors;
                        } else {
                            localStorage.setItem('token',token)
                            this.checkUser()
                        }
                    })
                    .catch(error => console.log(error));
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
            },
            errorClose(){
                this.errorMessages = ''
            }
        }
    }
</script>

<style scoped>
    .d-flex {
        display: flex;
        flex-wrap: wrap;
    }
    .alert.alert-danger {
        position: relative;
    }
    .alert span {
        width: 95%;
    }
    button.close {
        position: absolute;
        right: 10px;
        top: 5px;
    }
</style>