// resources/js/Pages/Tours/Index.jsx
import React, { useEffect, useState } from "react";
import { Link, router, usePage } from "@inertiajs/react";
import { api } from "../../lib/api";

export default function Index() {
    const { auth, pat, pat_scopes } = usePage().props;
    const [items, setItems] = useState([]);
    const client = api();

    // Save the PAT (shared once after login) so future requests use it
    useEffect(() => {
        if (pat) sessionStorage.setItem("access_token", pat);
        // if(pat) console.log(pat);
    }, [pat]);

    //   const canWrite  = pat_scopes?.includes('products:write');
    //   const canDelete = pat_scopes?.includes('products:delete');

    useEffect(() => {
        load();
    }, []);
    async function load() {
        const { data } = await client.get("/tours");
        setItems(data);
    }

    //   async function destroy(id) {
    //     await client.delete(`/products/${id}`);
    //     await load();
    //   }

    async function disconnect() {
        await client.post("/oauth/logout");
        sessionStorage.removeItem("access_token");
        router.post("/logout");
    }

    return (
        <div className="p-6 space-y-4">
            <div className="flex items-center justify-between">
                <h1 className="text-2xl font-bold">Tours</h1>
                <div className="flex items-center gap-2">
                    <span className="text-sm text-slate-600">
                        Signed in as <b>{auth?.user?.name}</b> (
                        {auth?.user?.role})
                    </span>
                    <button
                        onClick={disconnect}
                        className="px-3 py-2 rounded bg-slate-100"
                    >
                        Sign out
                    </button>
                </div>
            </div>

            <table className="min-w-full border mt-3">
                <thead>
                    <tr className="bg-slate-50">
                        <th className="p-2 border">#</th>
                        <th className="p-2 border">Name</th>
                        <th className="p-2 border">Price</th>
                        <th className="p-2 border">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    {items.map((p) => (
                        <tr key={p.tour_date.slice(0, 10)}>
                            <td className="p-2 border">
                                {p.tour_date.slice(0, 10)}
                            </td>
                            <td className="p-2 border">{p.remaining}</td>
                            <td className="p-2 border">{p.booked}</td>
                            <td className="p-2 border">{p.attended}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
