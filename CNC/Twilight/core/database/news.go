package database

import "sort"

type News struct {
	ID                   int
	Title, From, Content string
	Date                 int64
}

func (conn *Instance) GetNews() ([]*News, error) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 3")
	if err != nil {
		logger.Println("GetNews(): error occured while preparing statement \"" + err.Error() + "\"")
		return nil, err
	}
	result, err := stmt.Query()
	if err != nil {
		logger.Println("GetNews(): error occured while executing statement \"" + err.Error() + "\"")
		return nil, err
	}
	globalnews := make([]*News, 0)
	for result.Next() {
		news := new(News)
		if err := result.Scan(&news.ID, &news.Title, &news.From, &news.Content, &news.Date); err != nil {
			logger.Println("GetNews(): failed to scan flood! \"" + err.Error() + "\"")
		}
		globalnews = append(globalnews, news)
	}
	ReverseSlice(globalnews)
	return globalnews, nil
}

func (conn *Instance) NewNews(news *News) error {
    stmt, err := conn.conn.Prepare("INSERT INTO `news` (`title`, `from`, `content`, `date`) VALUES (?, ?, ?, ?)")
    if err != nil {
        logger.Println("NewNews(): error preparing SQL statement:", err)
        return err
    }
    defer stmt.Close()

    _, err = stmt.Exec(news.Title, news.From, news.Content, news.Date)
    if err != nil {
        logger.Println("NewNews(): error executing SQL statement:", err)
        return err
    }

    return nil
}

func ReverseSlice[T comparable](s []T) {
	sort.SliceStable(s, func(i, j int) bool {
		return i > j
	})
}
