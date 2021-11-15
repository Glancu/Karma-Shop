import { Link } from 'react-router-dom';
import React from 'react';

export const UserPanelHeader = () => {
    return (
        <div className="header">
            <ul>
                <Link to='/user/panel'><li>Change password</li></Link>
                <Link to='/user/orders'><li>My orders</li></Link>
            </ul>
        </div>
    )
}
