<?php

namespace App\BDSM;

trait ModelHelper {
    public function record($request) {
        if ($request instanceof \Illuminate\Http\Request) {
            $req = $request->all();
        } elseif (is_object($request)) {
            $req = (array) $request;
        } elseif (is_array($request)) {
            $req = $request;
        } else {
            $req = json_decode($req);
        }
        
        if (isset($req['password'])) {
            $req['password'] = bcrypt($req['password']);
        }

        $this->fill($req);
        $this->save();

        return $this;
    }

    public static function massRecord($request) {
        $requestData = [];
        foreach ($request as $rdtl) {
            $requestData[] = (new self())->record($rdtl);
        }
  
        return $requestData;
    }

    public function selectAll() {
        $filter = request('filter');
        $query = $this;
        if ($filter != null) {
            $fillable = $this->fillable;
            $query = $query->where(function($q) use ($fillable,$filter) {
                foreach ($fillable as $col) {
                    $q = $q->orWhere($col,'LIKE', '%' . $filter . '%');
                }
            });
            return $query;
        }
        return $query;
    }

    public function getTableProperties() {
        return [
            "table" => $this->table,
            "fields" => $this->fillable
        ];
    }
}