SELECT
    `userId`,
    `userLogin`,
    `userName`,
    `userHash`,
    `userHashUpdated`,
    `userEmail`,
    `userEmailUpdated`,
    `userEmailConfirmed`,
    `userMobile`,
    `userMobileUpdated`,
    `userMobileFonfirmed`,
    `roleId`,
    `managerId`
FROM 
    `user`
LIMIT
    :startingIndex, :recordLimit