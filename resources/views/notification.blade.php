<style>

    .search-form {
        width: 250px;
        margin: 10px 0 0 20px;
        border-radius: 3px;
        float: left;
    }
    .search-form input[type="text"] {
        color: #666;
        border: 0;
    }

    .search-form .btn {
        color: #999;
        background-color: #fff;
        border: 0;
    }

</style>

<form action="/admin/notification" method="post" class="search-form" >
    <div class="input-group input-group-sm ">
        <input type="text" name="messages" class="form-control" placeholder="请填写信息">
        <span class="input-group-btn button">
            <button type="submit" id="search-btn" class="btn btn-flat "><i class="fa fa-comments"></i> 推送消息 </button>
          </span>
    </div>
</form>
