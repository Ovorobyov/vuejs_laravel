<template>
    <div class="container">
        <div v-if="errorMessages" class="alert alert-danger">
            <span v-for="message in errorMessages">{{message[0]}}</span>
            <button type="button" class="close" aria-label="Close" @click.prevent="errorClose()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="">
            <form @submit.prevent="commentSend" id="comment" enctype="multipart/form-data" >
                <div v-show="replayingTo.id" class="alert alert-success">
                    <span>Ответ на комментарий: </span> <span>{{ replayingTo.message }}</span>
                    <button type="button" class="close" aria-label="Close" @click.prevent="unsetReplyingTo">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="form-group">
                    <label for="text">Текст комментария</label>
                    <textarea class="form-control" id="text" name="message" v-model="form.textarea.value" v-bind:rows="form.textarea.rows" v-bind:maxlength="form.textarea.maxLength"></textarea>
                </div>

                <div class="form-group">
                    <input type="file" id="file" ref="file" name="file"/>
                </div>

                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
        <div class="">
            <div class="comments">
                <h3 class="title-comments">Комментарии ({{pagination.total}})</h3>
                <ul class="media-list">

                    <li class="media" v-for="comment in comments">
                        <div class="media-left">
                            <a>
                                <img class="media-object img-rounded" src="/img/default.jpeg" alt="...">
                            </a>
                        </div>
                        <div class="media-body">
                            <div class="media-heading">
                                <div class="author">{{ comment.user.name }}</div>
                                <div class="metadata">
                                    <span class="date">{{comment.created_at}}</span>
                                </div>
                            </div>
                            <div class="media-text text-justify">
                                {{comment.message}}
                                <img class="img-responsive" v-if="comment.filename" v-bind:src="`${'/storage'}/${comment.filename}`" >
                            </div>
                            <div class="footer-comment">
                                <span class="vote plus" title="Нравится">
                                    <i class="fa fa-thumbs-up"></i>
                                </span>
                                <span class="rating">
                                    +{{(comment.replies)?comment.replies.length:0 }}
                                </span>
                                <span class="vote minus" title="Не нравится">
                                    <i class="fa fa-thumbs-down"></i>
                                </span>
                                <span class="devide">
                                   |
                                </span>
                                <span class="comment-reply">
                                    <a class="reply" @click.prevent="setReplayingTo(comment)">ответить</a>
                                </span>
                            </div>

                            <div v-for="reply in comment.replies" class="media">
                                <div class="media-left">
                                    <a href="#">
                                        <img class="media-object img-rounded" src="/img/default.jpeg" alt="">
                                    </a>
                                </div>
                                <div class="media-body">
                                    <div class="media-heading">
                                        <div class="author">{{ reply.user.name }}</div>
                                        <div class="metadata">
                                            <span class="date">{{reply.created_at}}</span>
                                        </div>
                                    </div>
                                    <div class="media-text text-justify">
                                        {{reply.message}}
                                        <img class="img-responsive" v-if="reply.filename" v-bind:src="`${'/storage'}/${reply.filename}`" >
                                    </div>
                                    <div class="footer-comment">
                                        <span class="vote plus" title="Нравится">
                                            <i class="fa fa-thumbs-up"></i>
                                        </span>
                                        <span class="vote minus" title="Не нравится">
                                            <i class="fa fa-thumbs-down"></i>
                                        </span>

                                        <span class="comment-reply">
                                            <a class="reply" @click.prevent="setReplayingTo(comment)">ответить</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <nav aria-label="Page navigation example" class="d-flex j-center">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled" v-show="!pagination.prev_page_url">
                            <a class="page-link" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item" v-show="pagination.prev_page_url">
                            <a class="page-link" v-on:click="fetchPaginateComments(pagination.prev_page_url)" tabindex="-1">Previous</a>
                        </li>
                        <li v-for="page in pagination.last_page" v-bind:class="[page == pagination.current_page ? 'active' : '']" class="page-item"><a v-on:click="fetchPaginateComments('/api/comments/?page='+page)" class="page-link">{{page}}</a></li>
                        <li class="page-item disabled" v-show="!pagination.next_page_url">
                            <a class="page-link">Next</a>
                        </li>
                        <li class="page-item" v-show="pagination.next_page_url">
                            <a class="page-link" v-on:click="fetchPaginateComments(pagination.next_page_url)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Home",
        data(){
            return {
                form: {
                    textarea: {
                        rows: 4,
                        maxLength: 1000,
                        value:""
                    },
                },
                comments: [],
                replayingTo: {},
                pagination: [],
                urlGetComments:'/api/comments',
                errorMessages:"",
            }
        },
        methods: {
            commentSend(){
                var form = document.forms.namedItem("comment");
                var oData = new FormData(form)

                oData.append('parent_id',(this.replayingTo.id)?this.replayingTo.id:'');
                axios.post('/api/comment/send', oData)
                    .then(response=>{
                        if (response.data.messages){
                            this.errorMessages = response.data.messages;
                        } else {
                            this.errorMessages = ''
                            this.form.textarea.value = ''
                            form.reset()

                            if (!this.replayingTo.id) {
                                this.comments.push(response.data)
                            } else {
                                this.fetchComments()
                            }

                            this.replayingTo = {}
                        }


                    })
            },
            fetchComments(){
                axios.get(this.urlGetComments)
                    .then(response=>{
                        this.makePagination(response.data)
                        this.comments = response.data.data
                    })
            },
            setReplayingTo(comment){
                this.replayingTo = comment
            },
            unsetReplyingTo(){
                this.replayingTo = {}
            },
            makePagination(data){
                let pagination = {
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page_url: data.next_page_url,
                    prev_page_url: data.prev_page_url,
                    total: data.total,
                }

                this.pagination = pagination
            },
            fetchPaginateComments(url){
                this.urlGetComments = url
                this.fetchComments()
            },
            errorClose(){
                this.errorMessages = ''
            }
        },
        created(){
            this.fetchComments()
        }
    }
</script>

<style>
    .media-body .author {
        display: inline-block;
        font-size: 1rem;
        color: #000;
        font-weight: 700;
    }
    .media-body .metadata {
        display: inline-block;
        margin-left: .5rem;
        color: #777;
        font-size: .8125rem;
    }
    .footer-comment {
        color: #777;
    }
    .vote.plus:hover {
        color: green;
    }
    .vote.minus:hover {
        color: red;
    }
    .vote {
        cursor: pointer;
    }
    .comment-reply a {
        color: #777;
    }
    .comment-reply a:hover, .comment-reply a:focus {
        color: #000;
        text-decoration: none;
    }
    .devide {
        padding: 0px 4px;
        font-size: 0.9em;
    }
    .media-text {
        margin-bottom: 0.25rem;
    }
    .title-comments {
        font-size: 1.4rem;
        font-weight: bold;
        line-height: 1.5rem;
        color: rgba(0, 0, 0, .87);
        margin-bottom: 1rem;
        padding-bottom: .25rem;
        border-bottom: 1px solid rgba(34, 36, 38, .15);
    }
    img.media-object.img-rounded {
        max-width: 50px;
    }
    .media-text img {
        max-width: 55%;
        margin-top: 10px;
    }
    .d-flex {
        display: flex;
    }
    .j-center {
        justify-content: center;
    }
</style>