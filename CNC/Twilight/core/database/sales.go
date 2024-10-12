package database

import "time"

type Sale struct {
	ID                     int
	UniqID                 string
	CryptoAmount, Recieved float64
	Amount, Parent         int
	Product, Address       string
	Status, Coin           string
	Date                   int64
}

func (conn *Instance) NewSale(sale *Sale) (int, error) {
	stmt, err := conn.conn.Prepare("INSERT INTO `sales` (`id`, `uniqid`, `amount`, `crypto_amount`, `crypto_address`, `recieved`, `coin`, `status`, `product`, `parent`, `date`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?,?,?,?)")
	if err != nil {
		return 0, err
	}
	result, err := stmt.Exec(sale.UniqID, sale.Amount, sale.CryptoAmount, sale.Address, 0, sale.Coin, sale.Status, sale.Product, sale.Parent, sale.Date)
	if err != nil {
		return 0, err
	}
	user, err := conn.GetUserByID(sale.Parent)
	if err != nil {
		return 0, err
	}
	user.Balance += sale.Amount
	if err := conn.UpdateUser(user); err != nil {
		return 0, err
	}
	id, _ := result.LastInsertId()
	return int(id), nil
}

func (conn *Instance) Sales() int {
	date := time.Date(time.Now().Year(), time.Now().Month(), time.Now().Day(), 0, 0, 0, 0, time.Local).Unix()
	stmt, err := conn.conn.Prepare("SELECT * FROM `sales` WHERE `date` > ?")
	if err != nil {
		return 0
	}
	rows, err := stmt.Query(date)
	if err != nil {
		return 0
	}
	var amount int
	for rows.Next() {
		sale, err := conn.scanSale(rows)
		if err != nil {
			return 0
		}
		amount += sale.Amount
	}
	return amount
}

func (conn *Instance) UpdateSale(sale *Sale) error {
	stmt, err := conn.conn.Prepare("UPDATE `sales` SET `recieved` = ?, `status` = ? WHERE `uniqid` = ?")
	if err != nil {
		return err
	}
	if _, err := stmt.Exec(sale.Recieved, sale.Status, sale.UniqID); err != nil {
		return err
	}
	return nil
}

func (conn *Instance) GetUserHistory(user *User) ([]*Sale, error) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `sales` WHERE `parent` = ?")
	if err != nil {
		return nil, err
	}
	rows, err := stmt.Query(user.ID)
	if err != nil {
		return nil, err
	}
	var sales []*Sale

	for rows.Next() {
		sale, err := conn.scanSale(rows)
		if err != nil {
			return nil, err
		}
		sales = append(sales, sale)
	}
	return sales, nil
}

func (conn *Instance) scanSale(query Query) (*Sale, error) {
	sale := new(Sale)
	err := query.Scan(
		&sale.ID,
		&sale.UniqID, &sale.Amount, &sale.CryptoAmount, &sale.Address, &sale.Recieved, &sale.Coin, &sale.Status, &sale.Product, &sale.Parent, &sale.Date,
	)
	return sale, err
}

func (conn *Instance) GetSale(id int) (*Sale, error) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `sales` WHERE `id` = ?")
	if err != nil {
		return nil, err
	}
	row := stmt.QueryRow(id)
	if err != nil {
		return nil, err
	}

	return conn.scanSale(row)

}

func (conn *Instance) GetSaleByUniq(uniq string) (*Sale, error) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `sales` WHERE `uniqid` = ?")
	if err != nil {
		return nil, err
	}
	row := stmt.QueryRow(uniq)
	if err != nil {
		return nil, err
	}

	return conn.scanSale(row)
}
