echo "update rooms set member_count = 2, status = 'xx', asker_user_name = 'user2' ;" | sqlite3 yenertuz ;
echo "insert into users (name, room_id) values ('user2', 1); " | sqlite3 yenertuz ;