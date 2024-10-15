alter table messages add column parentID int;

DELIMITER //
CREATE PROCEDURE `insertMessage` (in_parentId INT, in_senderId INT, in_receiverId INT, in_message TEXT, in_subject TEXT)
BEGIN
    insert into messages (parentID, sender_id, receiver_id, message, subject) values (in_parentId, in_senderId, in_receiverId, in_message, in_subject);
    set @id = (select last_insert_id());
    update messages set parentID = @id where id = @id and parentID = 0;
END//
DELIMITER ;