UPDATE
    `user`
SET
    `userMobile`=:userMobile,
    `userMobileUpdated`=:userMobileUpdated,
    `userMobileConfirmed`=:userMobileConfirmed
WHERE
    `userId`=:userId