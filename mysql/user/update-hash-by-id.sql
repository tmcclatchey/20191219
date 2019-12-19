UPDATE
    `user`
SET
    `userHash`=:userHash,
    `userHashUpdated`=:userHashUpdated
WHERE
    `userId`=:userId