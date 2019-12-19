UPDATE
    `user`
SET
    `userEmail`=:userEmail,
    `userEmailUpdated`=:userEmailUpdated,
    `userEmailConfirmed`=:userEmailConfirmed
WHERE
    `userId`=:userId